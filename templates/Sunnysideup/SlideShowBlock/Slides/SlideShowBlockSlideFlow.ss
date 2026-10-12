<% include SlideShowBlockSlideIntro %>
<% if $Items %>
<div class="slideshow-flow">
    <% loop $Items %>
    <% if not $IsFirst %><div class="slideshow-flow__arrow" aria-hidden="true">→</div><% end_if %>
    <div class="slideshow-flow__node<% if $Highlight %> slideshow-flow__node--highlight<% end_if %>">
        <b>$Title</b><% if $Text %><span>$Text</span><% end_if %>
    </div>
    <% end_loop %>
</div>
<% end_if %>
<% if $ExtraItems %>
<ul class="slideshow-list slideshow-list--2col">
    <% loop $ExtraItems %><li><strong>{$Title}:</strong> $Text</li><% end_loop %>
</ul>
<% end_if %>
<% include SlideShowBlockSlideOutro %>
