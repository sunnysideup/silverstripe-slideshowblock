<% include SlideShowBlockSlideIntro %>
<% if $Items %>
<div class="slideshow-flow">
    <% loop $Items %>
    <% if not $IsFirst %><div class="slideshow-flow__arrow" aria-hidden="true">→</div><% end_if %>
    <div class="slideshow-flow__node<% if $Highlight %> slideshow-flow__node--highlight<% end_if %>">
        <h4>$Title</h4>
        <% if $Text %><p>$Text</p><% end_if %>
    </div>
    <% end_loop %>
</div>
<% end_if %>
<% if $ExtraItems %>
<ul class="slideshow-list slideshow-list--2col">
    <% loop $ExtraItems %><li><h4>{$Title}:</h4><p>$Text</p></li><% end_loop %>
</ul>
<% end_if %>
<% include SlideShowBlockSlideOutro %>
