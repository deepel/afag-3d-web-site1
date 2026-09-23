<?php
$logoClass = $logoClass ?? 'mark';
$logoWidth = $logoWidth ?? null;
$logoHeight = $logoHeight ?? null;
$logoStyle = $logoStyle ?? '';
$logoAriaHidden = $logoAriaHidden ?? true;
$logoOpacity = $logoOpacity ?? null;
?>
<svg class="<?= htmlspecialchars($logoClass, ENT_QUOTES) ?>" viewBox="0 0 120 120"<?php echo $logoAriaHidden ? ' aria-hidden="true"' : ''; ?><?php echo $logoWidth !== null ? ' width="' . htmlspecialchars((string)$logoWidth, ENT_QUOTES) . '"' : ''; ?><?php echo $logoHeight !== null ? ' height="' . htmlspecialchars((string)$logoHeight, ENT_QUOTES) . '"' : ''; ?><?php echo $logoStyle !== '' ? ' style="' . htmlspecialchars($logoStyle, ENT_QUOTES) . '"' : ''; ?><?php echo $logoOpacity !== null ? ' opacity="' . htmlspecialchars((string)$logoOpacity, ENT_QUOTES) . '"' : ''; ?>>
  <polygon class="face-l" points="20,86 60,106 60,118 20,98"/>
  <polygon class="face-r" points="100,86 60,106 60,118 100,98"/>
  <polygon class="face-t" points="60,66 100,86 60,106 20,86"/>
  <polygon class="face-l" points="32,74 60,88 60,100 32,86"/>
  <polygon class="face-r" points="88,74 60,88 60,100 88,86"/>
  <polygon class="face-t" points="60,60 88,74 60,88 32,74"/>
  <polygon class="face-l" points="44,62 60,70 60,82 44,74"/>
  <polygon class="face-r" points="76,62 60,70 60,82 76,74"/>
  <polygon class="molten" points="60,54 76,62 60,70 44,62"/>
</svg>
