# 🏷️ Brand Menu Manager

[![WordPress](https://img.shields.io/badge/WordPress-4.0%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-5.6%2B-purple.svg)](https://php.net/)
[![Polylang](https://img.shields.io/badge/Polylang-Compatible-orange.svg)](https://polylang.pro/)
[![License](https://img.shields.io/badge/License-GPL%20v2%2B-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

A powerful WordPress plugin that automatically adds brand pages to navigation menus in bilingual WordPress sites with Polylang support. Perfect for service websites managing multiple appliance brands across different languages.

---

## ✨ Features

### 🌐 **Multilingual Support**
Full integration with Polylang for bilingual WordPress sites (Italian/English)

### 🎯 **Smart Brand Detection**
Automatically finds brand pages by matching brand names in page titles and slugs

### 📋 **Bulk Processing**
Process multiple brands at once with a simple list input

### 🔗 **Intelligent Menu Hierarchy**
Automatically creates parent-child relationships in menus based on page hierarchy

### 🛡️ **Duplicate Prevention**
Smart detection prevents adding the same brand pages multiple times

### ⚙️ **Flexible Configuration**
- Select specific parent pages for each language
- Choose target menus and assign languages
- Define appliance categories for organization

### 📊 **Detailed Reporting**
Comprehensive feedback showing what was added, skipped, or not found

---

## 🎯 Use Cases

Perfect for businesses offering appliance services across multiple brands:

| Service Type | Example Brands | Languages |
|--------------|----------------|-----------|
| 🧺 **Washing Machine Service** | Siemens, Bosch, Samsung, LG | IT/EN |
| ❄️ **Refrigerator Repair** | Whirlpool, Electrolux, Miele | IT/EN |
| 🍳 **Oven Maintenance** | AEG, Zanussi, Hotpoint | IT/EN |
| 🧽 **Dishwasher Service** | Indesit, Beko, Candy | IT/EN |

---

## 📦 Installation

1. Upload the plugin files to `/wp-content/plugins/brand-menu-manager/`
2. Activate the plugin through the **'Plugins'** screen in WordPress
3. Ensure **Polylang** is installed and configured with Italian/English languages
4. Navigate to **Tools → Brand Menu Manager** in your WordPress admin

---

## 🚀 Quick Start Guide

### Step 1: 📝 **Configure Settings**
```
Appliance Type: "washing machine service"
Italian Parent: Select Italian service page
English Parent: Select English service page
```

### Step 2: 📋 **Add Brand List**
```
Siemens
Bosch
Samsung
LG
Whirlpool
Electrolux
```

### Step 3: 🎯 **Select Target Menus**
- ✅ Choose menus to update
- 🌐 Assign language to each menu (IT/EN)

### Step 4: ▶️ **Process**
Click "Process Brands" and watch the magic happen!

---

## 🔧 How It Works

| Step | Process | Description |
|------|---------|-------------|
| 1️⃣ | **Page Discovery** | Finds brand pages under specified parent pages |
| 2️⃣ | **Language Matching** | Matches Italian/English versions of brand pages |
| 3️⃣ | **Menu Analysis** | Checks existing menu items to prevent duplicates |
| 4️⃣ | **Hierarchy Creation** | Adds brands as children of service category pages |
| 5️⃣ | **Reporting** | Provides detailed feedback on all operations |

---

## 🔍 Smart Brand Detection

The plugin uses intelligent matching algorithms:

- **Title Matching**: Searches page titles for brand names
- **Slug Matching**: Checks page URLs for brand identifiers  
- **Bidirectional Search**: Finds partial matches in both directions
- **Case Insensitive**: Works regardless of capitalization

---

## 🌐 Polylang Integration

### Supported Features:
- ✅ Automatic language detection
- ✅ Cross-language page relationships
- ✅ Menu language assignment
- ✅ Fallback language handling

### Fallback Behavior:
```
Menu Language: IT → Uses Italian brand pages
Menu Language: EN → Uses English brand pages  
Menu Language: Not set → Defaults to Italian, falls back to English
```

---

## ⚙️ Requirements

```
WordPress: 4.0+
PHP: 5.6+
Polylang: 2.0+ (recommended)
Permissions: Administrator privileges
```

### Optional Dependencies:
- **Polylang Pro**: Enhanced multilingual features
- **Custom Post Types**: For advanced content organization

---

## 🔒 Security Features

- ✅ **Nonce Verification**: All AJAX requests protected
- ✅ **Capability Checks**: Administrator-only access
- ✅ **Input Sanitization**: All user inputs properly sanitized
- ✅ **SQL Injection Protection**: Parameterized queries
- ✅ **XSS Prevention**: Output escaping throughout

---

## 📊 Processing Results

### Success Indicators:
- 🟢 **Added**: New brand successfully added to menu
- 🔵 **Skipped**: Brand already exists in menu
- 🟠 **Not Found**: Brand page doesn't exist

### Example Output:
```
✓ Added Siemens as child of "Washing Machine Service" in menus
⚠ Brand page not found for: UnknownBrand
⚪ Skipped Samsung (already exists in menus)

Summary: 4 brands added successfully, 1 brand skipped.
```

---

## 🛠️ Advanced Configuration

### Custom Appliance Types:
The plugin can be extended to support any service category:
- Air Conditioning Service
- Heating System Repair  
- Kitchen Appliance Maintenance
- Electronics Repair

### Database Structure:
```php
// Future expansion ready for:
- Custom appliance type taxonomies
- Brand-specific service mappings
- Multi-site network support
```

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add multilingual support'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Areas:
- 🌐 Additional language support
- 🔧 Custom post type integration
- 📱 Mobile-responsive admin interface
- 🚀 Performance optimizations

---

## 📄 License

This plugin is licensed under the **GPL v2 or later**.

---

## ⚠️ Important Notes

> **Backup Recommendation**: Always backup your menus before bulk processing. While the plugin prevents duplicates, it's always safer to have a backup.

> **Polylang Dependency**: While the plugin works without Polylang, multilingual features require Polylang to be active and properly configured.

---

## 📈 Stats

- 🎯 **Zero Database Changes** - Uses WordPress native functions
- ⚡ **AJAX-Powered** - Smooth user experience
- 🔧 **Hook-Based Architecture** - Easy to extend
- 🛡️ **Enterprise-Grade Security** - Multiple security layers
- 📱 **Responsive Design** - Works on all devices

---

## 🔄 Roadmap

### Version 1.1
- [ ] Visual menu preview
- [ ] Batch undo functionality
- [ ] Export/import configurations

### Version 1.2
- [ ] WooCommerce integration
- [ ] Custom taxonomies support
- [ ] Advanced filtering options

### Version 2.0
- [ ] Multi-site network support
- [ ] REST API endpoints
- [ ] Third-party integrations

---

<div align="center">

**Made with ❤️ for multilingual WordPress sites**

[Report Bug](../../issues) • [Request Feature](../../issues) • [Documentation](../../wiki)

*Perfect for service businesses managing appliance brands across languages*

</div>
