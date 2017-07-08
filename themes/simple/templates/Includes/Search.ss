<div class="searchform searchform--{$PageArea}" data-search-{$PageArea}>
    <form action="{$BaseHref}search" class="">
        <fieldset>
            <input name="searchterm"
                   class="searchform-input"
                   value="$SearchTerm()"
                   placeholder="Search"
                   type="search"
                   data-search-input
                   data-search-input-{$PageArea}
                   <% if $PageArea == 'menu' %>data-search-securityid="$SecurityID"<% end_if %>
                   autocapitalize="off"
                   autocomplete="off"
                   autocorrect="off"/>

            <button type="submit"
                    class="searchform-submit"
                    data-search-submit
                    data-search-submit-{$PageArea}>
                <span class="sr-only">Search</span>
            </button>
        </fieldset>
    </form>
</div>
