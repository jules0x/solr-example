<div class="searchheader">
    <h1 class="searchheader-title">
        <% if $Query %>
            <% if $Results.TotalResults > 0 %>
                Showing
            <% else %>
            <% end_if %><span class="searchheader-title--highlight">$Results.TotalResults</span> results for <span  class="searchheader--highlight">$Query</span>
        <% else %>
            Search
        <% end_if %>
    </h1>

    <% include SearchForm PageArea='desktop' %>

    <% if $Results %>
        <% if $ShowSuggestion %>
            <div class="searchheader-correction">
                <p class=searchheader-correction--suggestion">Showing results found for <span>$Suggestion</span></p>
                <p class=searchheader-correction--searchterm">No results were found matching <span>$Query</span></p>
            </div>
        <% end_if %>
    <% end_if %>
</div>

<div class="searchresults " data-search-results id="main" role="main">
    <% if $TotalResults > 0 %>
        <% include SearchResults Results=$Results %>
    <% else %>
        <p class="searchresults-noresult">
            <% if $Query %>
                No results
            <% else %>
                Please enter a search term
            <% end_if %>
        </p>
    <% end_if %>
</div>
