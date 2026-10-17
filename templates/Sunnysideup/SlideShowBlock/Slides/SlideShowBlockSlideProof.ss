<% include SlideShowBlockSlideIntro %>
<div class="slideshow-proof">
    <% if $StatNumber %>
    <div class="slideshow-proof__stat">
        <span class="slideshow-proof__number" data-count-up>$StatNumber</span>
        <% if $StatLabel %><span class="slideshow-proof__label">$StatLabel</span><% end_if %>
    </div>
    <% end_if %>
    <% if $Items %>
    <ul class="slideshow-proof__reasons">
        <% loop $Items %><li><h4>$Title</h4><% if $Text %><p>$Text</p><% end_if %></li><% end_loop %>
    </ul>
    <% end_if %>
</div>
<% include SlideShowBlockSlideOutro %>
