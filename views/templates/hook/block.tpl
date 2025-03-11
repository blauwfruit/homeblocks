<div class="homeblocks">
    <div class="row">
        {foreach from=$blocks item=item key=key name=name}
        <div class="{$item.classes}">
            <a href="{if $item.link}{$item.link}{else}javascript:void(0);{/if}">
                <div class="homeblock-list-item homeblock-list-item-{$key}">
                       <div style="color: {$item.text_color} !important;"> 
                        {if $item.name}
                            <h2 class='h2' style="color: {$item.text_color} !important;">{$item.name}</h2>
                        {/if}
                        {if $item.description}
                            <div class="homeblock-description homeblock-description-{$key}" style="color: {$item.text_color} !important;">
                                {$item.description nofilter}
                            </div>
                        {/if}
                    </div>

                    {if $item.classes|strstr:"button"}
                        
                        <button href="{$item.link}" class="btn btn-primary">{l s='Bekijken'}</button>
                    {/if}
                </div>
            </a>
        </div>
        <style>
            .homeblock-list-item-{$key} {
                {if $item.image}
                background-image: url({$item.image});
                {/if}
                background-color: {$item.background_color};
                background-position: {$item.image_position};
                background-size: {$item.image_size};
                color: {$item.text_color} !important;
            }

            .homeblock-description-{$key} * {
                color: {$item.text_color} !important;
            }
        </style>
        {/foreach}
    </div>
</div>
