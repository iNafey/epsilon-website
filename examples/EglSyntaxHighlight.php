<?
class EglSyntaxHighlight {
    public static function process( $s) {
        $s = htmlspecialchars( $s );
        
        // Workaround for escaped backslashes
        $s = str_replace( '\\\\','\\\\<e>', $s ); 
        
        $regexp = array(
          
						
						// Comments/Strings
            '/(
                \/\*.*?\*\/|
                --.*?\n|
                &quot;.*?&quot;|
                \'(.*?)\'
            )/isex' 
            => 'self::replaceId($tokens,\'$1\')',
						

            // Numbers (also look for Hex)
            '/(?<!\w)(
                0x[\da-f]+|
                \d+
            )(?!\w)/ix'
            => '<span class="N">$1</span>',
            
            // Make the bold assumption that an all uppercase word has a 
            // special meaning
            '/(?<!\w|>)(
                [A-Z_0-9]{2,}
            )(?!\w)/x'
            => '<span class="D">$1</span>', 
            
            // Keywords
            '/(?<!\w|\$|\%|\@|>)('.self::getKeywords().')(?!\w|=")/'
            => '<span style="color:#7F0055;font-weight:bold">$1</span>', 

            // Keywords
            '/(?<!\w|\$|\%|\@|>)(self)(?!\w|=")/'
            => '<span style="color:#2A00FF">$1</span>', 
            
            // PHP/Perl-Style Vars: $var, %var, @var
            '/(?<!\w)(
                (\$|\%|\@)(\-&gt;|\w)+
            )(?!\w)/ix'
            => '<span class="V">$1</span>',
						
						//EGL regions
						'/(\[%|%\])/'
						=> '<span style="background-color:#FBF2B8">$1</span>'
						
        );
        
        $tokens = array(); // This array will be filled from the regexp-callback
        $s = preg_replace( array_keys($regexp), array_values($regexp), $s );
        
        // Paste the comments and strings back in again
        $s = str_replace( array_keys($tokens), array_values($tokens), $s );
        
        // Delete the "Escaped Backslash Workaround Token" (TM) and replace 
        // tabs with four spaces.
        $s = str_replace( array( '<e>', "\t" ), array( '', '  ' ), $s );
        
        return '<pre>'.$s.'</pre>';
    }
    
		private static function getKeywords() {
			$keywords = "delete|import|for|while|in|and|or|operation|return|def|var|throw|if|new|else|transaction|abort|break|continue|assert|assertError|not";
			
			return $keywords;
		}
		
    // Regexp-Callback to replace every comment or string with a uniqid and save 
    // the matched text in an array
    // This way, strings and comments will be stripped out and wont be processed 
    // by the other expressions searching for keywords etc.
    private static function replaceId( &$a, $match ) {
        $id = "##r".uniqid()."##";
        
        // String or Comment?
        if( $match{0} == '-') {
            $a[$id] = '<span style="color:#3F7F5F">'.$match.'</span>';
        } else {
            $a[$id] = '<span style="color:#2A00FF">'.$match.'</span>';
        }
        return $id;
    }
}
?>