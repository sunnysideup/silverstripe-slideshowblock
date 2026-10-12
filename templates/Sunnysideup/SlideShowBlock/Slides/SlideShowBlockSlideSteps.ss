<% include SlideShowBlockSlideIntro %>
<% if $Items %>
<ol class="slideshow-steps">
    <% loop $Items %>
    <li class="slideshow-card"><h4 class="slideshow-card__title">$Title</h4><% if $Text %><p>$Text</p><% end_if %></li>
    <% end_loop %>
</ol>
<% end_if %>
<% if $CTALabel && $CTALink %>
<a class="slideshow-cta" href="$CTALink.ATT">$CTALabel <span aria-hidden="true">→</span></a>
<% end_if %>
<% include SlideShowBlockSlideOutro %>
