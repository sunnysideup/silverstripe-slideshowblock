<% include SlideShowBlockSlideIntro %>
<% if $Items %>
<div class="slideshow-cards slideshow-cards--$CardStyleClass slideshow-cards--cols-$ColumnsClass">
    <% loop $Items %>
    <article class="slideshow-card<% if $Highlight %> slideshow-card--highlight<% end_if %>">
        <% if $IconSVG %><span class="slideshow-icon" aria-hidden="true">$IconSVG</span><% end_if %>
        <h4 class="slideshow-card__title">$Title</h4>
        <% if $Text %><p>$Text</p><% end_if %>
    </article>
    <% end_loop %>
</div>
<% end_if %>
<% include SlideShowBlockSlideOutro %>
