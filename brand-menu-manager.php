<?php
/**
 * Plugin Name: Brand Menu Manager
 * Description: Automatically adds brand pages to menus in bilingual WordPress sites with Polylang
 * Version: 1.0
 * Author: TerraChad / Vladyslav Olshevskyi
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class BrandMenuManager {
    
    private $plugin_slug = 'brand-menu-manager';
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_process_brand_menu', array($this, 'process_brand_menu_ajax'));
    }
    
    public function add_admin_menu() {
        add_management_page(
            'Brand Menu Manager',
            'Brand Menu Manager',
            'manage_options',
            $this->plugin_slug,
            array($this, 'admin_page')
        );
    }
    
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'tools_page_' . $this->plugin_slug) {
            return;
        }
        
        wp_enqueue_script('jquery');
        wp_localize_script('jquery', 'brand_menu_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('brand_menu_nonce')
        ));
    }
    
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>Brand Menu Manager</h1>
            
            <form id="brand-menu-form">
                <?php wp_nonce_field('brand_menu_nonce', 'brand_menu_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="appliance_type">Appliance Type</label>
                        </th>
                        <td>
                            <input type="text" id="appliance_type" name="appliance_type" 
                                   value="washing machine service" class="regular-text" 
                                   placeholder="e.g., washing machine service, refrigerator repair" />
                            <p class="description">The appliance category (used to find parent pages)</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="italian_parent">Italian Parent Page</label>
                        </th>
                        <td>
                            <?php 
                            wp_dropdown_pages(array(
                                'name' => 'italian_parent',
                                'id' => 'italian_parent',
                                'show_option_none' => 'Select Italian Parent Page',
                                'option_none_value' => '',
                            ));
                            ?>
                            <p class="description">Select the Italian parent page (e.g., washing machine service IT)</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="english_parent">English Parent Page</label>
                        </th>
                        <td>
                            <?php 
                            wp_dropdown_pages(array(
                                'name' => 'english_parent',
                                'id' => 'english_parent',
                                'show_option_none' => 'Select English Parent Page',
                                'option_none_value' => '',
                            ));
                            ?>
                            <p class="description">Select the English parent page (e.g., washing machine service EN)</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="brand_list">Brand List</label>
                        </th>
                        <td>
                            <textarea id="brand_list" name="brand_list" rows="10" cols="50" class="large-text" 
                                      placeholder="Siemens&#10;Bosch&#10;Samsung&#10;LG&#10;Whirlpool"></textarea>
                            <p class="description">Enter one brand per line. These names will be used in the menu.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Target Menus</th>
                        <td>
                            <?php
                            $menus = wp_get_nav_menus();
                            if (empty($menus)) {
                                echo '<p>No menus found. Please create some menus first.</p>';
                            } else {
                                foreach ($menus as $menu) {
                                    echo '<div style="margin-bottom: 10px; padding: 5px; border: 1px solid #ddd; border-radius: 3px;">';
                                    echo '<label><input type="checkbox" name="target_menus[]" value="' . esc_attr($menu->term_id) . '"> <strong>' . esc_html($menu->name) . '</strong></label><br>';
                                    echo '<select name="menu_language[' . esc_attr($menu->term_id) . ']" style="margin-top: 5px; width: 200px;">';
                                    echo '<option value="">Select Language</option>';
                                    
                                    // Get Polylang languages if available
                                    if (function_exists('pll_languages_list')) {
                                        $languages = pll_languages_list();
                                        if (!empty($languages)) {
                                            foreach ($languages as $lang) {
                                                $lang_name = function_exists('pll_get_language_name') ? pll_get_language_name($lang) : ucfirst($lang);
                                                echo '<option value="' . esc_attr($lang) . '">' . esc_html($lang_name) . '</option>';
                                            }
                                        } else {
                                            // Fallback if no languages configured
                                            echo '<option value="it">Italian</option>';
                                            echo '<option value="en">English</option>';
                                        }
                                    } else {
                                        // Fallback if Polylang not active
                                        echo '<option value="it">Italian</option>';
                                        echo '<option value="en">English</option>';
                                    }
                                    
                                    echo '</select>';
                                    echo '</div>';
                                }
                            }
                            ?>
                            <p class="description">Select menus and specify their language</p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Process Brands">
                </p>
            </form>
            
            <div id="processing-results" style="display: none;">
                <h3>Processing Results</h3>
                <div id="results-content"></div>
            </div>
        </div>
        
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#brand-menu-form').on('submit', function(e) {
                e.preventDefault();
                
                $('#submit').prop('disabled', true).val('Processing...');
                $('#processing-results').show();
                $('#results-content').html('<p>Processing brands...</p>');
                
                $.ajax({
                    url: brand_menu_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'process_brand_menu',
                        nonce: brand_menu_ajax.nonce,
                        appliance_type: $('#appliance_type').val(),
                        italian_parent: $('#italian_parent').val(),
                        english_parent: $('#english_parent').val(),
                        brand_list: $('#brand_list').val(),
                        target_menus: $('input[name="target_menus[]"]:checked').map(function() {
                            return this.value;
                        }).get(),
                        menu_languages: (function() {
                            var languages = {};
                            $('select[name^="menu_language"]').each(function() {
                                var match = this.name.match(/menu_language\[(\d+)\]/);
                                if (match) {
                                    languages[match[1]] = $(this).val();
                                }
                            });
                            return languages;
                        })()
                    },
                    success: function(response) {
                        $('#results-content').html(response.data);
                        $('#submit').prop('disabled', false).val('Process Brands');
                    },
                    error: function() {
                        $('#results-content').html('<p style="color: red;">An error occurred while processing.</p>');
                        $('#submit').prop('disabled', false).val('Process Brands');
                    }
                });
            });
        });
        </script>
        <?php
    }
    
    public function process_brand_menu_ajax() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'brand_menu_nonce')) {
            wp_die('Security check failed');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $appliance_type = sanitize_text_field($_POST['appliance_type']);
        $italian_parent = intval($_POST['italian_parent']);
        $english_parent = intval($_POST['english_parent']);
        $brand_list = sanitize_textarea_field($_POST['brand_list']);
        $target_menus = array_map('intval', $_POST['target_menus']);
        $menu_languages = array();
        
        // Process menu languages - they come as an object now
        if (isset($_POST['menu_languages']) && is_array($_POST['menu_languages'])) {
            foreach ($_POST['menu_languages'] as $menu_id => $language) {
                $menu_languages[intval($menu_id)] = sanitize_text_field($language);
            }
        }
        
        $results = $this->process_brands($appliance_type, $italian_parent, $english_parent, $brand_list, $target_menus, $menu_languages);
        
        wp_send_json_success($results);
    }
    
    private function process_brands($appliance_type, $italian_parent, $english_parent, $brand_list, $target_menus, $menu_languages) {
        $brands = array_filter(array_map('trim', explode("\n", $brand_list)));
        $results = '<div class="notice notice-info"><p><strong>Processing Results:</strong></p></div>';
        
        $italian_children = $this->get_child_pages($italian_parent);
        $english_children = $this->get_child_pages($english_parent);
        
        $processed_count = 0;
        $skipped_count = 0;
        $not_found_brands = array();
        $skipped_brands = array();
        
        foreach ($brands as $brand) {
            $italian_page = $this->find_brand_page($brand, $italian_children);
            $english_page = $this->find_brand_page($brand, $english_children);
            
            if ($italian_page || $english_page) {
                $hierarchy_info = $this->add_to_menus($brand, $italian_page, $english_page, $target_menus, $menu_languages);
                
                if ($hierarchy_info['was_skipped']) {
                    $skipped_count++;
                    $skipped_brands[] = $brand;
                    $results .= '<p style="color: blue;">⚪ Skipped ' . esc_html($brand) . ' (already exists in menus)</p>';
                } else {
                    $processed_count++;
                    if ($hierarchy_info['has_parent']) {
                        $results .= '<p style="color: green;">✓ Added ' . esc_html($brand) . ' as child of "' . esc_html($hierarchy_info['parent_title']) . '" in menus</p>';
                    } else {
                        $results .= '<p style="color: green;">✓ Added ' . esc_html($brand) . ' to menus (no parent found)</p>';
                    }
                }
            } else {
                $not_found_brands[] = $brand;
                $results .= '<p style="color: orange;">⚠ Brand page not found for: ' . esc_html($brand) . '</p>';
            }
        }
        
        $results .= '<div class="notice notice-success"><p><strong>Summary:</strong> ' . $processed_count . ' brands added successfully';
        if ($skipped_count > 0) {
            $results .= ', ' . $skipped_count . ' brands skipped (already existed)';
        }
        $results .= '.</p></div>';
        
        if (!empty($skipped_brands)) {
            $results .= '<div class="notice notice-info"><p><strong>Skipped (already in menus):</strong> ' . implode(', ', array_map('esc_html', $skipped_brands)) . '</p></div>';
        }
        
        if (!empty($not_found_brands)) {
            $results .= '<div class="notice notice-warning"><p><strong>Not found:</strong> ' . implode(', ', array_map('esc_html', $not_found_brands)) . '</p></div>';
        }
        
        return $results;
    }
    
    private function get_child_pages($parent_id) {
        return get_pages(array(
            'parent' => $parent_id,
            'post_status' => 'publish'
        ));
    }
    
    private function find_brand_page($brand, $pages) {
        $brand_lower = strtolower($brand);
        
        foreach ($pages as $page) {
            $title_lower = strtolower($page->post_title);
            $slug_lower = strtolower($page->post_name);
            
            // Check if brand name is contained in title or slug
            if (strpos($title_lower, $brand_lower) !== false || 
                strpos($slug_lower, $brand_lower) !== false ||
                strpos($brand_lower, $title_lower) !== false) {
                return $page;
            }
        }
        
        return null;
    }
    
    private function add_to_menus($brand_name, $italian_page, $english_page, $target_menus, $menu_languages) {
        $hierarchy_info = array('has_parent' => false, 'parent_title' => '', 'was_skipped' => false);
        $items_added = 0;
        $items_skipped = 0;
        
        foreach ($target_menus as $menu_id) {
            $menu_obj = wp_get_nav_menu_object($menu_id);
            if (!$menu_obj) continue;
            
            // Get the language for this specific menu
            $menu_language = isset($menu_languages[$menu_id]) ? $menu_languages[$menu_id] : '';
            
            // Determine which page to use based on menu language
            $page_to_add = null;
            $parent_page = null;
            
            if ($menu_language === 'it' && $italian_page) {
                $page_to_add = $italian_page;
                $parent_page = get_post($italian_page->post_parent);
            } elseif ($menu_language === 'en' && $english_page) {
                $page_to_add = $english_page;
                $parent_page = get_post($english_page->post_parent);
            } elseif (($menu_language === 'it' || empty($menu_language)) && $italian_page) {
                // Fallback to Italian if language not specified and Italian page exists
                $page_to_add = $italian_page;
                $parent_page = get_post($italian_page->post_parent);
            } elseif ($english_page) {
                // Fallback to English page
                $page_to_add = $english_page;
                $parent_page = get_post($english_page->post_parent);
            }
            
            if (!$page_to_add) continue;
            
            // Check if menu item already exists
            $existing_items = wp_get_nav_menu_items($menu_id);
            $exists = false;
            $parent_menu_item_id = 0;
            
            // Check if brand already exists and find parent menu item
            foreach ($existing_items as $item) {
                // Check if brand already exists by PAGE ID (not by title)
                if ($item->object_id == $page_to_add->ID && $item->object == 'page') {
                    $exists = true;
                    $items_skipped++;
                    break;
                }
                
                // Look for parent page in menu (if we have a parent page)
                if ($parent_page && $item->object_id == $parent_page->ID && $item->object == 'page') {
                    $parent_menu_item_id = $item->ID;
                    $hierarchy_info['has_parent'] = true;
                    $hierarchy_info['parent_title'] = $parent_page->post_title;
                }
            }
            
            if (!$exists) {
                $menu_item_args = array(
                    'menu-item-title' => $brand_name,
                    'menu-item-object' => 'page',
                    'menu-item-object-id' => $page_to_add->ID,
                    'menu-item-type' => 'post_type',
                    'menu-item-status' => 'publish'
                );
                
                // If we found a parent in the menu, make this a child
                if ($parent_menu_item_id > 0) {
                    $menu_item_args['menu-item-parent-id'] = $parent_menu_item_id;
                }
                
                wp_update_nav_menu_item($menu_id, 0, $menu_item_args);
                $items_added++;
            }
        }
        
        // If all items were skipped, mark as skipped
        if ($items_skipped > 0 && $items_added === 0) {
            $hierarchy_info['was_skipped'] = true;
        }
        
        return $hierarchy_info;
    }
    
    // Helper method for future expansion to handle different appliance types
    public function get_appliance_parent_pages($appliance_type, $language = '') {
        $args = array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'appliance_type',
                    'value' => $appliance_type,
                    'compare' => 'LIKE'
                )
            )
        );
        
        if ($language && function_exists('pll_get_post')) {
            // If Polylang is active, filter by language
            $args['lang'] = $language;
        }
        
        return get_posts($args);
    }
}

// Initialize the plugin
new BrandMenuManager();

// Activation hook to create necessary database tables if needed in future
register_activation_hook(__FILE__, function() {
    // Future: Create custom tables for appliance types and brand mappings
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Clean up if necessary
});
?>
