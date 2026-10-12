<% if $Kicker || $Heading || $Lead %>
<div class="slideshow-slide__intro">
    <% if $Kicker %><p class="slideshow-slide__kicker">$Kicker</p><% end_if %>
    <% if $Heading %><h3 class="slideshow-slide__heading">$HeadingHTML</h3><% end_if %>
    <% if $Lead %><p class="slideshow-slide__lead">$Lead</p><% end_if %>
</div>
<% end_if %>
