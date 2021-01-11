<?php
global $query_municipio;
?>
</div>
</div>
</div>

<div class="container-fluid marPad0">
    <div class="googlemap_wrap marPad0">
        <iframe
            src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBpUejQpYFS2opJHv5UCk2TLX6OCTLNAMc
    &q=<?php echo urlencode($query_municipio);?>"
            width="100" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false"
            tabindex="0"></iframe>
    </div>
</div>

