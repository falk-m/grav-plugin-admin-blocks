# Grav Admin Addon Page Blocks Plugin

![Config](./assets/intro.png)

Add a modular way do manage content of a page like other CMS, e.g. Kirby or Redaxo.


## usage

### extend page types

```yaml
header.blocks:
    type: blocks
    label: Blöcke
    style: vertical
    fields:
        .headline:
            type: section
            label: Headline
            fields:
                .content:
                    type: text
                    label: Text
                .subtext:
                    type: text
                    label: Subtext
        .text:
            type: editor
            label: Inhalt
        .image:
            type: filepicker
            folder: 'self@'
            preview_images: true
            label: Image
```

### edit page in admin

![Config](./assets/demo.png)

### or edit page in editor

```yaml
---
title: 'Block Test'
media_order: 'testbild.jpg,testbild1.jpg'
blocks:
    block-1:
        headline:
            content: 'Test Headline'
            subtext: subtext
    block-2:
        text: 'Lorem ipsum dolor sit...'
    block-3:
        image: testbild.jpg
---
```

### extend the theme

![Config](./assets/page.png)

```html
{% set blocks = (page.header.blocks is null ? [] : page.header.blocks) %}

{% for block_element in blocks %}
    {% set block_type = block_element | keys | first %}

    <div class="blocks-{{block_type}}">
        
    {% set block_content = block_element[block_type] %}

        {% include 'partials/blocks/' ~ block_type ~ '.html.twig' ignore missing with {content: block_content} %}

    </div>
{% endfor %}
```

`path/to/active/theme/templates/partials/blocks/headline.html.twig`

```html
<h2>{{ content.content}}</h2>
{% if content.subtext %}
<h3>{{ content.subtext}}</h3>
{% endif %}
```


`path/to/active/theme/templates/partials/blocks/text.html.twig`

```
{{ content|markdown() }}
```

`path/to/active/theme/templates/partials/blocks/image.html.twig`

```
{{ page.media[content].resize(200,200).html()|raw }}
```


## Installation

### Grav Package Manager (GPM)

If you can access your Grav installation via the command line, install the plugin by typing the following from your Grav root:

```
bin/gpm install admin-addon-blocks
```

### Admin Tool Web Interface

In the **Plugins** section, hit the **[+ Add]** button, search for Admin Addon Page Blocks and install.

### Manual Installation

To install the plugin manually, download the ZIP version of this repository and unzip it under `/your/site/grav/user/plugins`. Then rename the folder to `admin-addon-blocks`.

## Configuration

### Admin Tool Web Interface

![Config](./assets/config.png)

### Manual configuration

`/your/site/grav/user/config/plugins/admin-addon-blocks.yaml`

```yaml
enabled: true
use_demo: false
```