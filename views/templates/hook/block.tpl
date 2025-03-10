<div class="homeblocks">
    <div class="row">
        {foreach from=$blocks item=item key=key name=name}
        <div class="{$item.classes}">
            <a href="{if $item.link}{$item.link}{else}javascript:void(0);{/if}">
                <div class="homeblock-list-item" style="
                    {if $item.image}background-image: url({$item.image});
                    {/if} background-color: {$item.background_color};
                    color: {$item.text_color} !important;
                ">
                       <div style="color: {$item.text_color} !important;"> 
                        {if $item.name}
                            <h2 class='h2' style="color: {$item.text_color} !important;">{$item.name}</h2>
                        {/if}
                        {if $item.description}
                            <div class="homeblock-description homeblock-description-{$key}" style="color: {$item.text_color} !important;">
                                {$item.description nofilter}
                            </div>
                            <style>
                                .homeblock-description-{$key} * {
                                    color: {$item.text_color} !important;
                                }
                            </style>
                        {/if}
                    </div>
                    {if $item.classes|strstr:"button"}
                        <button href="{$item.link}" class="btn btn-primary">{l s='Bekijken'}</button>
                    {/if}
                </div>
            </a>
        </div>
        {/foreach}
    </div>
</div>
