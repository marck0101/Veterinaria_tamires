<?php
function seo_tags(array $opts = []): void {
    $base    = 'http://localhost:8000';
    $titulo  = $opts['titulo']  ?? 'Tamires Müller — Médica Veterinária em São Martinho, RS';
    $desc    = $opts['desc']    ?? 'Atendimento veterinário especializado em bovinos leiteiros e gerenciamento de propriedades rurais na região de São Martinho, RS — parceria SENAR.';
    $url     = $base . ($_SERVER['REQUEST_URI'] ?? '/');
    $imagem  = $opts['imagem']  ?? $base . '/assets/img/og-default.jpg';
    $tipo    = $opts['tipo']    ?? 'website';
    $palavras = $opts['keywords'] ?? 'veterinária São Martinho RS, bovinos leiteiros, gerenciamento propriedade rural, SENAR, médica veterinária interior RS, manejo bovino, consultoria rural';
?>
  <title><?= htmlspecialchars($titulo) ?></title>
  <meta name="description"        content="<?= htmlspecialchars($desc) ?>">
  <meta name="keywords"           content="<?= htmlspecialchars($palavras) ?>">
  <meta name="author"             content="Tamires Müller">
  <meta name="robots"             content="index, follow">
  <meta name="geo.region"         content="BR-RS">
  <meta name="geo.placename"      content="São Martinho, Rio Grande do Sul">

  <!-- Open Graph -->
  <meta property="og:title"       content="<?= htmlspecialchars($titulo) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($desc) ?>">
  <meta property="og:url"         content="<?= htmlspecialchars($url) ?>">
  <meta property="og:type"        content="<?= $tipo ?>">
  <meta property="og:image"       content="<?= htmlspecialchars($imagem) ?>">
  <meta property="og:locale"      content="pt_BR">
  <meta property="og:site_name"   content="Tamires Müller Serviços Veterinários">

  <!-- Schema.org — Veterinarian (LLM-friendly) -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Veterinarian",
    "name": "Tamires Müller Serviços Veterinários",
    "description": "<?= addslashes($desc) ?>",
    "url": "<?= $base ?>",
    "telephone": "+55-55-96959566",
    "email": "tamires@email.com",
    "priceRange": "$$",
    "image": "<?= htmlspecialchars($imagem) ?>",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "São Martinho",
      "addressRegion": "RS",
      "addressCountry": "BR"
    },
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": "-27.7",
      "longitude": "-54.0"
    },
    "areaServed": [
      "São Martinho", "Boa Vista do Buricá", "Sede Nova", "Nova Candelária", "
    ],
    "medicalSpecialty": "Bovinocultura leiteira",
    "availableService": [
      {"@type": "MedicalTherapy", "name": "Clínica e medicina preventiva"},
      {"@type": "MedicalTherapy", "name": "Gerenciamento de propriedade rural"},
      {"@type": "MedicalTherapy", "name": "Sanidade de rebanho"},
      {"@type": "MedicalTherapy", "name": "Mochamento e casqueamento"},
      {"@type": "MedicalTherapy", "name": "Consultoria SENAR"}
    ],
    "sameAs": ["https://instagram.com/"]
  }
  </script>
<?php } ?>