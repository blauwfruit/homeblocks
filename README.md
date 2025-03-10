# Home blocks

A flexible module that adds blocks to your homepage; add an image, CSS-classes, a link and background-color. Move the order of the blocks around easily!

## How to contribute

1. Create a bug report or feature request
2. Creata a PR to solve and mention the issue you created in step 1

## Docker

For development or demo purposes you can run Docker to test this integration.

For the latest PrestaShop:
```bash
gh repo clone blauwfruit/homeblocks .
docker compose up
```

For other version

```bash
gh repo clone blauwfruit/homeblocks .
docker compose down --volumes && export TAG=8.1.7-8.1-apache && docker compose up
```