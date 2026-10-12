<% include SlideShowBlockSlideIntro %>
<% if $Items %>
<div class="slideshow-timeline">
    <% loop $BarsForTemplate %>
    <div class="slideshow-timeline__row">
        <div class="slideshow-timeline__label"><b>$Title</b><% if $Text %><span>$Text</span><% end_if %></div>
        <div class="slideshow-timeline__bar"><span class="slideshow-timeline__fill" style="--w: {$BarWidth}%">$Label</span></div>
    </div>
    <% end_loop %>
</div>
<% end_if %>
<% include SlideShowBlockSlideOutro %>
