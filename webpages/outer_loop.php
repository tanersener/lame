<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>LAME MP3 Encoder :: GPSYCHO - outer_loop() Algorithm</title>
    <meta name="author" content="Roberto Amorim - rjamorim@yahoo.com" />
    <meta name="generator" content="jEdit 5.4" />
    <meta name="cvs-version" content="$Id: outer_loop.php,v 1.5 2009-11-03 16:11:01 rjamorim Exp $" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="styles/lame.css" />
    <!--[if IE 6]>
    <link rel="stylesheet" type="text/css" href="styles/ie.css" />
    <![endif]-->
</head>
<body>

<?php include("menu.html") ?>

<div id="container">
<div id="content">

<div align="center">
    <img src="images/logo.gif" width="358" height="231" alt="LAME Official Logo" />
    <h1>GPSYCHO - outer_loop() Algorithm</h1>
</div>

<p>
    Based on <cite>Bosi et al. "ISO/IEC MPEG-2 AAC", J. Audio Eng. Soc. 45 (1997)
    p 789-814</cite>.
</p>

<p>
    Another good complement to the ISO documentation is <cite>Brandenburg &amp;
    Stoll, "ISO-MPEG-1 Audio: A Generic Standard for Coding of High-Quality
    Digital Audio", J. Audio Eng. Soc 42 (1994) p 780-792</cite>.
</p>

<p>
    The goal of the outer_loop routine in MP3 is to find the combination of
    scalefactors within each scalefactor band which produce the least amount of
    audible distortion. Audible distortion is distortion in a scalefactor band
    which exceeds the masking thresholds (computed by the psycho-acoustic model)
</p>

<p>
    Pseudo-Code:
</p>

<blockquote class="code">initialize all scalefactors to 0.

compute initial q = quantization step size (bin_search_stepsize)
    divide &amp; conquer algorithm to find approximate value of q

outer_loop:
do {

   compute quantization with given scalefactors and not too many bits
   (call inner_loop)

   calc_noise():
      compute distortion within each scalefactor band
      compare distortion to allowed distortion (from psy-model)
      over = number of scalefactor bands where distortion > allowed_distortion
      tot_noise = average, over all bands of distortion(db) - allowed_distortion(db)
      over_noise= as tot_noise, but only bands with distortion > allowed_distortion

   if this quantization is the best one found so far (determined by quant_compare())
   save it.

   if over=0 we are done, exit.

   amplify scalefactors for bands with distortion

} while over&lt;>0 or !(all scalefactors set to their max)

Restore BEST quantization</blockquote>

<p>
    Whenever a scalefactor band is amplified, it will force the next quantization
    to use more bits for that band. This will result in more bits used to encode
    the MDCT coefficients in that band, and thus less quantization error. That is
    why bands with audible distortion are amplified. However, it will also result
    in less bits for the unamplified bands. But these bands had a quantization
    error less than the allowed masking, so hopefully they can tolerate a little
    more noise. The whole procedure is designed to allocate the bits to the
    bands which need them the most.
</p>

<p>
    When the loop is done, if we found a quantization with count=0, everything is
    great. Otherwise, we have to choose the best quantization that we found. The
    ISO model chooses the last quantization tried during outer_loop. This is
    strange because this is usually one of the worst. The MPEG2 paper makes the
    obvious point that after trying out all the different combinations, you
    should choose the BEST one, not the LAST one!
</p>

<p>
    GPSYCHO defines the BEST quantization via the subroutine quant_compare(), and
    this subroutine is in a constant state of flux!
</p>

<p>
    If you have ideas for a better way to define the BEST quantization, let me
    know!
</p>

<p>
    Gabriel Bouvigne makes the following point: Which do you think is worse?
</p>

<ul>
    <li>
        Distortion only in band 9? (1.9-2.2kHz) (over = 1)
    </li>
    <li>
        Distortion in band 0 (0-172Hz) and band 20 (13-16kHz) (over = 2)
    </li>
</ul>

<p>
    The outer_loop() described above will choose the over=1 as the best
    quantization.
</p>

</div>
<?php include("footer.html") ?>
</div>

</body>
</html>