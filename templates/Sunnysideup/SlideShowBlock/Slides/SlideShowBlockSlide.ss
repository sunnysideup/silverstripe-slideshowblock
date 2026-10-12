<%-- Fallback for slide types without their own template. --%>
<% include SlideShowBlockSlideIntro %>
<% if $Items %>
<ul class="slideshow-list">
    <% loop $Items %><li><% if $Text %><strong>$Title</strong> $Text<% else %>$Title<% end_if %></li><% end_loop %>
</ul>
<% end_if %>
<% include SlideShowBlockSlideOutro %>
