<% loop $Results %>
    <div class="searchresults-result">
        <a class="searchresults-link" href="$Link">
            <p class="searchresults-crumbs">$SearchCrumbs</p>
            <span class="searchresults-date">$Created.Format('d M Y')</span>
            <h3 class="searchresults-title">$Title</h3>
        </a>
    </div>
<% end_loop %>
