# Config Override

A Grav plugin that overrides page frontmatter and theme config at runtime via URL query params. No file writes, no cache changes, per-request only.

**Keep disabled in production.**

## Usage

### Page Config (`config_page`, `config_pages`)

**Single page** — target one page by route:

Syntax:
```
?config_page[target-route][key]=value
```

Multiple keys on one page:

Syntax:
```
?config_page[target-route][key-1]=value-1&config_page[target-route][key-2]=value-2
```

Example — change a course icon (non-routable page, visited via child page):
```
https://demo.hibbittsdesign.org/grav-helios-course-hub/cpt-363-1/home?config_page[cpt-363-1][icon]=tabler/rocket.svg
```
[https://demo.hibbittsdesign.org/grav-helios-course-hub/cpt-363-1/home?config_page[cpt-363-1][icon]=tabler/rocket.svg](https://demo.hibbittsdesign.org/grav-helios-course-hub/cpt-363-1/home?config_page[cpt-363-1][icon]=tabler/rocket.svg)

**Multiple pages** — use the route as the array key (no leading slash needed):

Syntax:
```
?config_pages[target-route-1][key]=value&config_pages[target-route-2][key]=value
```

Example — enable course images across all three courses:
```
https://demo.hibbittsdesign.org/grav-helios-course-hub/?config_pages[cpt-363-1][image]=daria-nepriakhina-614246-unsplash.jpg&config_pages[cpt-363-2][image]=daria-nepriakhina-ZH4CdJRAh0A-unsplash.jpg&config_pages[cpt-363-3][image]=daria-nepriakhina-zoCDWPuiRuA-unsplash.jpg
```
[https://demo.hibbittsdesign.org/grav-helios-course-hub/?config_pages[cpt-363-1][image]=daria-nepriakhina-614246-unsplash.jpg&config_pages[cpt-363-2][image]=daria-nepriakhina-ZH4CdJRAh0A-unsplash.jpg&config_pages[cpt-363-3][image]=daria-nepriakhina-zoCDWPuiRuA-unsplash.jpg](https://demo.hibbittsdesign.org/grav-helios-course-hub/?config_pages[cpt-363-1][image]=daria-nepriakhina-614246-unsplash.jpg&config_pages[cpt-363-2][image]=daria-nepriakhina-ZH4CdJRAh0A-unsplash.jpg&config_pages[cpt-363-3][image]=daria-nepriakhina-zoCDWPuiRuA-unsplash.jpg)

Example — combine layout and image changes in one request:
```
https://demo.hibbittsdesign.org/grav-helios-course-hub/?config_pages[courses][card_image_layout]=side&config_pages[courses][cards_per_row]=2&config_pages[cpt-363-1][image]=daria-nepriakhina-614246-unsplash.jpg
```
[https://demo.hibbittsdesign.org/grav-helios-course-hub/?config_pages[courses][card_image_layout]=side&config_pages[courses][cards_per_row]=2&config_pages[cpt-363-1][image]=daria-nepriakhina-614246-unsplash.jpg](https://demo.hibbittsdesign.org/grav-helios-course-hub/?config_pages[courses][card_image_layout]=side&config_pages[courses][cards_per_row]=2&config_pages[cpt-363-1][image]=daria-nepriakhina-614246-unsplash.jpg)

Both modes can target any page, routable or not, by its route.

### Theme Config (`config_theme`)

Override any theme config value for the current request. Targets the theme set in the plugin's `theme_name` setting (default: `helios`):

Syntax:
```
?config_theme[key]=value
```

Multiple keys:

Syntax:
```
?config_theme[key-1]=value-1&config_theme[key-2]=value-2
```

Example — change the body font size (options: small, medium, large):
```
https://demo.hibbittsdesign.org/grav-helios-course-hub/cpt-363-1/home?config_theme[fonts.body_size]=large
```
[https://demo.hibbittsdesign.org/grav-helios-course-hub/cpt-363-1/home?config_theme[fonts.body_size]=large](https://demo.hibbittsdesign.org/grav-helios-course-hub/cpt-363-1/home?config_theme[fonts.body_size]=large)

Example — change the body font (options: inter, open-sans, geom, nunito-sans, ubuntu-sans, work-sans, public-sans, quicksand):
```
https://demo.hibbittsdesign.org/grav-helios-course-hub/cpt-363-1/home?config_theme[fonts.body]=quicksand
```
[https://demo.hibbittsdesign.org/grav-helios-course-hub/cpt-363-1/home?config_theme[fonts.body]=quicksand](https://demo.hibbittsdesign.org/grav-helios-course-hub/cpt-363-1/home?config_theme[fonts.body]=quicksand)

Theme config values apply globally to the entire rendered page. The values `true` and `false` are cast to booleans, and numeric values are cast to numbers.

All three params — `config_page`, `config_pages`, and `config_theme` — can be combined in one request.

## Keys

Any page config key can be set. Use dot notation for nested keys:

Syntax:
```
?config_page[target-route][key.subkey]=value
```

## Non-routable pages

`config_page` works on non-routable pages (e.g. a course root `course.md` with `routable: false`). Since you can't visit a non-routable page directly, add the params to any reachable child page instead:

Syntax:
```
/child-page?config_page[non-routable-route][key]=value
```

The plugin finds the target page by route internally, regardless of its routable setting.

## Notes

- Grav's page cache can prevent overrides on cached URLs. Disable caching during dev, or visit the page via an alternate URL (e.g. `/` instead of `/courses`)
- Values are sanitized with `strip_tags` before being applied
- The following page config keys are blocked for security: `redirect`, `access`, `template`, `process`
