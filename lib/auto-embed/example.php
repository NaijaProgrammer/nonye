<!DOCTYPE html>
<html>
    <head>
        <title>AutoEmbed Examples</title>
    </head>
    <body>
        <h1>AutoEmbed Examples</h1>
        <?php
        $content = "
        <p>Have a laugh...</p>
        http://youtu.be/dMH0bHeiRNg
        <p>Hahahaha, awesome!</p>
        <p>Want to learn something interesting?</p>
        http://blip.tv/bushcraft/m-for-morocco-azbushcraft-com-1738329
        <p>Something else perhaps?</p>
        http://vimeo.com/62571137
        <p>Maybe music is more your style?</p>
        https://soundcloud.com/nanaperadze/f-chopin-dernier-nocturne
        <p>A stylized photo could be just the ticket.</p>
        http://instagram.com/p/BUG/
        <p>Now that's easy.</p>
        ";
        echo '<h2>Before</h2>';
        echo '<pre>'.htmlspecialchars($content, ENT_QUOTES, 'UTF-8').'</pre>';
        echo '<h2>After</h2>';
        require 'autoembed.php';
        $autoembed = new AutoEmbed();
        //$html_content = $autoembed->parse($content);
       // echo $html_content;
		
		echo '<hr>';
		var_dump( $autoembed->data('http://youtu.be/dMH0bHeiRNg') );
        ?>
    </body>
</html>