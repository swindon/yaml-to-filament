# YAML to Filament

A Laravel Filament package that allows developers to define models, resources, pages, widgets, and relationships using YAML configuration files. This package automatically generates Filament components using an artisan command.

## Installation

Install via composer:

```sh
composer config repositories.swindon/yaml-to-filament vcs "https://github.com/swindon/yaml-to-filament" && composer require swindon/yaml-to-filament:dev-main
```

Install manually by adding to composer.json file

```json
{
    "require": {
        "swindon/yaml-to-filament": "dev-main"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/swindon/yaml-to-filament"
        }
    ]
}
```

Publish the config file:

```sh
php artisan vendor:publish --tag=yaml-to-filament-config
```

## Usage

### 1. Define Your YAML Configuration

Create a YAML file inside the `resources/yaml-to-filament/` directory:

```sh
resources/yaml-to-filament/example.yaml
```

### 2. Run the Generator Command

Run the artisan command to generate Filament components:

```sh
php artisan filament:generate-from-yaml example.yaml
```

or generate all YAML files in the directory:

```sh
php artisan filament:generate-from-yaml
```

## YAML Configuration

### **Example YAML Configuration**
```yaml
models:
  User:
    table: users
    fillable:
      - name
      - email
      - password
    relationships:
      roles:
        type: belongsToMany
        model: Role
        pivot_table: role_user

  Post:
    table: posts
    fillable:
      - title
      - content
      - user_id
    relationships:
      user:
        type: belongsTo
        model: User
        foreign_key: user_id
      comments:
        type: hasMany
        model: Comment

resources:
  UserResource:
    model: User
    icon: heroicon-o-user
    form:
      fields:
        - type: text
          name: name
          label: Full Name
          required: true
        - type: email
          name: email
          label: Email Address
          required: true
        - type: password
          name: password
          label: Password
    table:
      columns:
        - type: text
          name: name
          label: Full Name
        - type: email
          name: email
          label: Email
      actions:
        - type: edit
        - type: delete
    filters:
      - type: select
        name: roles
        label: Role
        relation: roles
        options: roles

pages:
  UserDashboard:
    icon: heroicon-o-chart-bar
    actions:
      - type: button
        label: Refresh Stats
        action: refresh

widgets:
  RevenueWidget:
    view: widgets.revenue-widget
    data:
      chartType: line
      query: SELECT date, total FROM revenues
```

---

### **Explanation of YAML Keys**
- **models:** Defines database models, relationships, and fillable attributes.
- **resources:** Defines Filament resources with icons, forms, tables, actions, and filters.
- **pages:** Defines Filament pages with icons and actions.
- **widgets:** Defines dashboard widgets with view files and dynamic data.

## Contribution

Feel free to contribute via pull requests. Issues and feature requests are welcome!

## License

This package is licensed under the [MIT License](https://opensource.org/licenses/MIT).