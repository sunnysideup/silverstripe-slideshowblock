<div class="slideshow-slide__intro">
    <% if $Kicker %><p class="slideshow-slide__kicker">$Kicker</p><% end_if %>
    <h3 class="slideshow-slide__heading slideshow-title__heading">$HeadingHTML</h3>
    <% if $SubTitle %><p class="slideshow-title__sub">$SubTitle</p><% end_if %>
    <% if $Lead %><p class="slideshow-title__tag">$Lead</p><% end_if %>
</div>
<% if $Items %>
<ul class="slideshow-title__pills">
    <% loop $Items %><li class="slideshow-title__pill">$Title</li><% end_loop %>
</ul>
<% end_if %>
<svg class="slideshow-title__wave" viewBox="0 0 480 40" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" aria-hidden="true">
    <path d="M4 20c20-16 40-16 60 0s40 16 60 0 40-16 60 0 40 16 60 0 40-16 60 0 40 16 60 0 40-16 60 0 40 16 56 0"/>
</svg>
