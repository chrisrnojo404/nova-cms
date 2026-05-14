<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Media;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Plugin;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CmsBootstrapSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@nova-cms.test'],
            [
                'name' => 'Nova Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles(['super-admin']);

        Setting::storeMany([
            ['group' => 'general', 'key' => 'site_name', 'value' => ['value' => 'Nova CMS'], 'is_public' => true, 'autoload' => true],
            ['group' => 'general', 'key' => 'site_tagline', 'value' => ['value' => 'Commercial-ready Laravel CMS foundation'], 'is_public' => true, 'autoload' => true],
            ['group' => 'general', 'key' => 'site_email', 'value' => ['value' => 'hello@nova-cms.test'], 'is_public' => false, 'autoload' => true],
            ['group' => 'branding', 'key' => 'active_theme', 'value' => ['value' => 'default'], 'is_public' => true, 'autoload' => true],
            ['group' => 'branding', 'key' => 'brand_accent', 'value' => ['value' => '#22d3ee'], 'is_public' => true, 'autoload' => true],
            ['group' => 'homepage', 'key' => 'homepage_mode', 'value' => ['value' => 'preview'], 'is_public' => true, 'autoload' => true],
            ['group' => 'homepage', 'key' => 'homepage_page_id', 'value' => ['value' => null], 'is_public' => true, 'autoload' => true],
            ['group' => 'reading', 'key' => 'posts_per_page', 'value' => ['value' => 9], 'is_public' => true, 'autoload' => true],
            ['group' => 'media', 'key' => 'media_upload_directory', 'value' => ['value' => 'media/uploads'], 'is_public' => false, 'autoload' => true],
            ['group' => 'media', 'key' => 'image_quality', 'value' => ['value' => 82], 'is_public' => false, 'autoload' => true],
        ]);

        Theme::updateOrCreate(
            ['slug' => 'default'],
            [
                'name' => 'Default Theme',
                'version' => '1.0.0',
                'author' => 'CRN CMS',
                'description' => 'Default Nova CMS frontend theme scaffold.',
                'path' => 'themes/default',
                'is_active' => true,
                'meta' => ['type' => 'frontend'],
            ]
        );

        Plugin::updateOrCreate(
            ['slug' => 'contact-form'],
            [
                'name' => 'Contact Form',
                'version' => '1.0.0',
                'author' => 'CRN CMS',
                'description' => 'Example plugin scaffold for future shortcode and widget support.',
                'path' => 'plugins/contact-form',
                'is_active' => false,
                'meta' => ['shortcodes' => ['contact_form']],
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'about-nova'],
            [
                'author_id' => $admin->id,
                'title' => 'About Nova CMS',
                'content' => '<p>Nova CMS is a modular Laravel content platform focused on scalable publishing workflows, extensible architecture, and a polished admin experience.</p><p>Phase 2 begins with structured page management so teams can create, edit, publish, and render website content end to end.</p>',
                'status' => 'published',
                'template' => 'default',
                'meta_title' => 'About Nova CMS',
                'meta_description' => 'Learn about the Nova CMS platform foundation and publishing workflow.',
                'blocks' => [
                    ['type' => 'heading', 'content' => 'About Nova CMS'],
                    ['type' => 'paragraph', 'content' => 'Nova CMS is a modular Laravel content platform focused on scalable publishing workflows, extensible architecture, and a polished admin experience.'],
                    ['type' => 'paragraph', 'content' => 'Phase 2 begins with structured page management so teams can create, edit, publish, and render website content end to end.'],
                ],
                'published_at' => now(),
            ]
        );

        foreach ([
            [
                'name' => 'Announcements',
                'slug' => 'announcements',
                'description' => 'Platform and release updates for the Nova CMS product.',
                'meta_title' => 'Announcements',
                'meta_description' => 'Platform and release updates for Nova CMS.',
            ],
            [
                'name' => 'Guides',
                'slug' => 'guides',
                'description' => 'Editorial and implementation guides for teams using Nova CMS.',
                'meta_title' => 'Guides',
                'meta_description' => 'Editorial and implementation guides for Nova CMS.',
            ],
        ] as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }

        $announcements = Category::query()->where('slug', 'announcements')->first();

        Post::updateOrCreate(
            ['slug' => 'nova-cms-phase-2-begins'],
            [
                'author_id' => $admin->id,
                'category_id' => $announcements?->id,
                'title' => 'Nova CMS Phase 2 Begins',
                'excerpt' => 'Phase 2 introduces structured content management with pages, categories, and the foundation for a production-ready blog.',
                'content' => '<p>Nova CMS is moving from platform foundation into usable content workflows.</p><p>The first Phase 2 milestones introduce managed pages, category taxonomies, and the blog architecture that will support editorial publishing.</p>',
                'status' => 'published',
                'featured_image' => null,
                'meta_title' => 'Nova CMS Phase 2 Begins',
                'meta_description' => 'Phase 2 introduces structured content management with pages, categories, and the foundation for a production-ready blog.',
                'blocks' => [
                    ['type' => 'heading', 'content' => 'Nova CMS Phase 2 Begins'],
                    ['type' => 'paragraph', 'content' => 'Nova CMS is moving from platform foundation into usable content workflows.'],
                    ['type' => 'paragraph', 'content' => 'The first Phase 2 milestones introduce managed pages, category taxonomies, and the blog architecture that will support editorial publishing.'],
                ],
                'published_at' => now(),
            ]
        );

        Media::updateOrCreate(
            ['path' => 'media/placeholders/nova-cover.webp'],
            [
                'user_id' => $admin->id,
                'disk' => 'public',
                'directory' => 'media/placeholders',
                'filename' => 'nova-cover.webp',
                'original_name' => 'nova-cover.webp',
                'mime_type' => 'image/webp',
                'extension' => 'webp',
                'url' => '/storage/media/placeholders/nova-cover.webp',
                'size' => 184320,
                'alt_text' => 'Nova CMS placeholder cover image',
            ]
        );

        $headerMenu = Menu::updateOrCreate(
            ['slug' => 'primary-header'],
            [
                'name' => 'Primary Header',
                'location' => 'header',
                'description' => 'Main public navigation for the Nova CMS preview and frontend pages.',
                'is_active' => true,
            ]
        );

        $footerMenu = Menu::updateOrCreate(
            ['slug' => 'footer-links'],
            [
                'name' => 'Footer Links',
                'location' => 'footer',
                'description' => 'Footer navigation for key content entry points.',
                'is_active' => true,
            ]
        );

        MenuItem::updateOrCreate(
            ['menu_id' => $headerMenu->id, 'title' => 'Home'],
            [
                'linked_type' => 'custom',
                'linked_id' => null,
                'url' => route('home'),
                'target' => 'same_tab',
                'position' => 0,
                'is_active' => true,
            ]
        );

        MenuItem::updateOrCreate(
            ['menu_id' => $headerMenu->id, 'title' => 'About Nova'],
            [
                'linked_type' => 'page',
                'linked_id' => Page::query()->where('slug', 'about-nova')->value('id'),
                'url' => null,
                'target' => 'same_tab',
                'position' => 1,
                'is_active' => true,
            ]
        );

        MenuItem::updateOrCreate(
            ['menu_id' => $headerMenu->id, 'title' => 'Blog'],
            [
                'linked_type' => 'custom',
                'linked_id' => null,
                'url' => route('posts.index'),
                'target' => 'same_tab',
                'position' => 2,
                'is_active' => true,
            ]
        );

        MenuItem::updateOrCreate(
            ['menu_id' => $footerMenu->id, 'title' => 'Announcements'],
            [
                'linked_type' => 'category',
                'linked_id' => Category::query()->where('slug', 'announcements')->value('id'),
                'url' => null,
                'target' => 'same_tab',
                'position' => 0,
                'is_active' => true,
            ]
        );
    }
}
