# Changelog

## [Unreleased] - 2025-12-13

### Fixed - Language Switcher Implementation

#### Problem
- Language selector would change but menu always remained in Portuguese
- Session keys were inconsistent across different parts of the application
- LoadTenantMenu middleware always loaded default language instead of user selection

#### Solution

**Session Key Standardization**
- Standardized all session keys to use `lang` (language code) and `language_id`
- Removed inconsistent keys: `tenant_frontend_lang`, `frontend_lang`

**Files Modified:**

1. **app/Http/Middleware/LoadTenantMenu.php** (NEW)
   - Now reads `lang` and `language_id` from session
   - Falls back to default language only if session is empty
   - Properly loads menu based on user's selected language

2. **app/Http/Controllers/Front/FrontendController.php**
   - Updated `changeLanguage()` method
   - Now uses `User\Language` model for tenant-specific languages
   - Saves both `lang` code and `language_id` to session

3. **app/Http/Controllers/UserFrontend/MiscellaneousController.php**
   - Updated `changeLanguage()` method
   - Changed from `tenant_frontend_lang` to `lang`
   - Added `language_id` storage in session

4. **app/Http/Middleware/TenantFrontendLocale.php**
   - Standardized to use `lang` session key
   - Removed `tenant_frontend_lang` references

#### Result
- ✅ Language selector now works correctly
- ✅ Menus display in correct language (EN, ES, PT)
- ✅ Language persists across page navigation
- ✅ Session state properly maintained

#### Technical Details

**Language IDs (Tenant 148):**
- English (EN): `language_id: 300`
- Español (ES): `language_id: 306`
- Português (PT): `language_id: 315` (default)

**Session Flow:**
1. User selects language from dropdown
2. `MiscellaneousController::changeLanguage()` saves `lang` and `language_id` to session
3. `LoadTenantMenu` middleware reads session and loads correct menu
4. Menu displays in selected language

---

## Debugging Commands Used
```bash
# Check session contents
php artisan tinker
session()->all();

# Monitor logs
tail -f /var/log/php8.3-fpm.log | grep "🔵\|✅"

# Clear all caches
php artisan cache:clear
php artisan view:clear
systemctl reload php8.3-fpm
```

