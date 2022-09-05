<div class="homeblocks">
    <div class="row">
        {foreach from=$blocks item=item key=key name=name}
        <div class="{$item.classes}">
            <a href="{if $item.link}{$item.link}{else}javascript:void(0);{/if}">
                <div class="homeblock-list-item" style="{if $item.image}background-image: url({$item.image});{/if} background-color: {$item.background_color};">
                       <div> 
                        {if {$item.name}}
                            <h2 class='h2'>{$item.name}</h2>
                        {/if}
                        {if {$item.description}}
                            <p class="homeblock-description">
                                {$item.description}
                            </p>
                        {/if}
                    </div>
                    {if !$item.classes|strstr:"cover"}
                        <button href="{$item.link}" class="btn btn-primary">{l s='Bekijken'}</button>
                    {/if}
                </div>
            </a>
        </div>
        {/foreach}
    </div>
</div>
