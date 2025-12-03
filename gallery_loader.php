<?php
if(isset($_GET['city'])){
    $city = basename($_GET['city']);
    $folder = "photos/$city";
    $thumbFolder = "$folder/thumbnails";

    if(!is_dir($thumbFolder)) mkdir($thumbFolder, 0755, true);

    if(is_dir($folder)){
        $files = array_diff(scandir($folder), array('.', '..'));

        foreach($files as $file){
            $path = "$folder/$file";

            // Kép
            if(preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)){
                echo '<div class="col-6 col-md-4">';
                echo '<a href="'.$path.'" class="glightbox" data-gallery="gallery">';
                echo '<img src="'.$path.'" alt="">';
                echo '</a></div>';
            }

            // Videó
            elseif(preg_match('/\.(mp4|webm|ogg)$/i', $file)){
                $baseName = pathinfo($file, PATHINFO_FILENAME);
                $thumbPath = "$thumbFolder/$baseName.jpg";

                if(!file_exists($thumbPath)){
                    // Kiolvassuk a rotation metaadatot
                    $rotationOutput = shell_exec("ffprobe -v error -select_streams v:0 -show_entries stream_tags=rotate -of default=nw=1:nk=1 ".escapeshellarg($path));
                    $rotation = intval(trim($rotationOutput));

                    $transpose = '';
                    switch($rotation){
                        case 90: $transpose = 'transpose=1'; break;
                        case 180: $transpose = 'transpose=2,transpose=2'; break;
                        case 270: $transpose = 'transpose=2'; break;
                        default: $transpose = ''; break;
                    }

                    $vf = 'scale=320:-1';
                    if($transpose) $vf .= ','.$transpose;

                    $cmd = "ffmpeg -i ".escapeshellarg($path)." -ss 00:00:01 -vframes 1 -vf \"$vf\" -y ".escapeshellarg($thumbPath);
                    exec($cmd);
                }

                echo '<div class="col-6 col-md-4">';
                echo '<a href="'.$path.'" class="glightbox" data-gallery="gallery" data-type="video" data-autoplay="false">';
                echo '<img src="'.$thumbPath.'" alt="">';
                echo '</a></div>';
            }
        }
    } else {
        echo "<p class='text-center'>Nincs galéria ehhez a városhoz.</p>";
    }
}
?>
