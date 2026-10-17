<% include SlideShowBlockSlideIntro %>
<% if $Items %>
<div class="slideshow-timeline">
    <% loop $BarsForTemplate %>
    <div class="slideshow-timeline__row">
        <div class="slideshow-timeline__label"><h4>$Title</h4><% if $Text %><p class="no-top-margin small">$Text</p><% end_if %></div>
        <div class="slideshow-timeline__bar"><span class="slideshow-timeline__fill" style="--w: {$BarWidth}%">$Label</span></div>
    </div>
    <% end_loop %>
</div>
<% end_if %>
<% include SlideShowBlockSlideOutro %>
