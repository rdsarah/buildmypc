<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\BuildTemplate;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuildSuggestionAndTemplateTest extends TestCase
{
    use RefreshDatabase;

    private array $categories = [];
    private array $products = [];

    protected function setUp(): void
    {
        parent::setUp();

        $brand = Brand::create(['name' => 'Gigabyte', 'slug' => 'gigabyte']);

        $categoryNames = ['CPU', 'Motherboard', 'RAM', 'GPU', 'Storage', 'PSU', 'Casing'];
        foreach ($categoryNames as $name) {
            $this->categories[$name] = Category::create([
                'name' => $name,
                'slug' => strtolower($name)
            ]);
        }

        // Create a few cheap and expensive products in each category
        foreach ($this->categories as $name => $category) {
            $this->products[$name . '_cheap'] = Product::create([
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'name' => $name . ' Cheap',
                'slug' => strtolower($name) . '-cheap',
                'price' => 2000.00,
                'stock' => 10,
                'is_active' => true,
                'wattage' => 50,
            ]);

            $this->products[$name . '_expensive'] = Product::create([
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'name' => $name . ' Expensive',
                'slug' => strtolower($name) . '-expensive',
                'price' => 20000.00,
                'stock' => 5,
                'is_active' => true,
                'wattage' => 100,
            ]);
        }
    }

    public function test_user_can_auto_suggest_build_under_budget(): void
    {
        $response = $this->post(route('builder.suggest'), [
            'budget' => 30000, // Small budget, should select cheap components
            'focus' => 'gaming'
        ]);

        $response->assertRedirect();
        $this->assertTrue(session()->has('active_build'));

        $activeBuild = session()->get('active_build');
        // Sum total price of active build items
        $totalPrice = Product::whereIn('id', array_values($activeBuild))->sum('price');
        
        $this->assertLessThanOrEqual(30000, $totalPrice);
    }

    public function test_user_can_clone_build_template(): void
    {
        $template = BuildTemplate::create([
            'name' => 'Office Starter',
            'description' => 'A basic starting build for offices',
            'target_use' => 'office'
        ]);

        $template->products()->attach([
            $this->products['CPU_cheap']->id,
            $this->products['Motherboard_cheap']->id,
        ]);

        $response = $this->post(route('builder.clone', $template->id));

        $response->assertRedirect();
        $activeBuild = session()->get('active_build');
        
        $this->assertEquals($this->products['CPU_cheap']->id, $activeBuild['CPU']);
        $this->assertEquals($this->products['Motherboard_cheap']->id, $activeBuild['Motherboard']);
    }

    public function test_admin_can_create_starter_build_template(): void
    {
        $response = $this->post(route('admin.templates.create'), [
            'name' => 'Special Admin Build',
            'description' => 'A special admin build template description',
            'target_use' => 'gaming',
            'products' => [
                $this->products['CPU_expensive']->id,
                $this->products['GPU_expensive']->id,
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('build_templates', [
            'name' => 'Special Admin Build',
            'target_use' => 'gaming'
        ]);

        $template = BuildTemplate::where('name', 'Special Admin Build')->first();
        $this->assertCount(2, $template->products);
    }

    public function test_admin_can_delete_starter_build_template(): void
    {
        $template = BuildTemplate::create([
            'name' => 'Temporary Build',
            'description' => 'Will be deleted soon',
            'target_use' => 'office'
        ]);

        $response = $this->delete(route('admin.templates.delete', $template->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('build_templates', [
            'id' => $template->id
        ]);
    }
}
