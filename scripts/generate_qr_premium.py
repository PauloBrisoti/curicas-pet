#!/usr/bin/env python3
"""
============================================================
GERADOR DE QR CODES PREMIUM v4.0 — Curicas Pet
============================================================
Changelog v4.0:
  - Centro: logo COMPLETA (símbolo + texto "Curicas Pet"),
    mantendo proporção exata da imagem, sem distorção.
  - Quiet zone circular BRANCA, limpa, sem borda grossa.
  - Módulos: micro-patinhas (pad oval + 4 toe beans) em #25D366.
  - Finder patterns: #2D6A0A, bordas muito suavizadas.
  - Saída padrão: 2400×2400px @ 300 DPI (qualidade gráfica).
============================================================
"""

import qrcode
import math
import os
from PIL import Image, ImageDraw

# ── CAMINHOS ────────────────────────────────────────────────────
SCRIPT_DIR  = os.path.dirname(os.path.abspath(__file__))
PROJECT_DIR = os.path.dirname(SCRIPT_DIR)
LOGO        = os.path.join(PROJECT_DIR, 'assets', 'images', 'logo-curicas-pet.png')
OUTPUT_DIR  = os.path.join(PROJECT_DIR, 'assets', 'images', 'qrcodes')

# ── DIMENSÕES ───────────────────────────────────────────────────
QR_DEFAULT   = 2400   # resolução padrão para gráfica (300 DPI)
QUIET_MARGIN = 4      # quiet zone externa em módulos (padrão ISO)

# ── PALETA ──────────────────────────────────────────────────────
GREEN_DATA   = (37, 211, 102)   # #25D366 — verde vibrante (módulos)
GREEN_FINDER = (45, 106, 10)    # #2D6A0A — verde-escuro (finder patterns)
BG_WHITE     = (255, 255, 255)

# ── CAMPANHAS ───────────────────────────────────────────────────
CAMPAIGNS = [
    {
        'url':   'https://curicaspet.com.br/camiseta',
        'file':  'qr-camiseta.png',
        'label': 'Campanha de Rua (Camiseta)',
        'size':  2400,
    },
    {
        'url':   'https://curicaspet.com.br/avaliar',
        'file':  'qr-avaliar.png',
        'label': 'Avaliações Google',
        'size':  2400,
    },
]


# ────────────────────────────────────────────────────────────────
# HELPERS — MATRIZ QR
# ────────────────────────────────────────────────────────────────

def generate_qr_matrix(url: str):
    """Gera a matriz binária com nível H (30% de redundância)."""
    qr = qrcode.QRCode(
        version=None,
        error_correction=qrcode.constants.ERROR_CORRECT_H,
        box_size=1,
        border=0,
    )
    qr.add_data(url)
    qr.make(fit=True)
    return qr.get_matrix()


def is_finder(row: int, col: int, n: int) -> bool:
    if row < 7 and col < 7:       return True   # top-left
    if row < 7 and col >= n - 7:  return True   # top-right
    if row >= n - 7 and col < 7:  return True   # bottom-left
    return False


def is_alignment(row: int, col: int, n: int) -> bool:
    if n <= 21:
        return False
    ac = n - 7 - 2
    return abs(row - ac) <= 2 and abs(col - ac) <= 2


# ────────────────────────────────────────────────────────────────
# GEOMETRIA — PAW PRINT DETALHADA
# ────────────────────────────────────────────────────────────────

def draw_paw(draw: ImageDraw.ImageDraw, cx: float, cy: float,
             s: float, color: tuple):
    """
    Patinha nítida em célula s×s:
      • Pad oval largo na metade inferior
      • 4 toe beans em arco acima, com gap visível em relação ao pad
    """
    # Almofada central
    pad_w  = s * 0.60
    pad_h  = s * 0.41
    pad_cy = cy + s * 0.13
    draw.ellipse(
        [cx - pad_w, pad_cy - pad_h,
         cx + pad_w, pad_cy + pad_h],
        fill=color
    )

    # 4 dedos (toe beans)
    toe_rx = s * 0.150
    toe_ry = s * 0.170
    toe_base_y = pad_cy - pad_h - s * 0.06   # topo do pad com gap

    toe_positions = [
        (-s * 0.280,  s * 0.038),
        (-s * 0.093, -s * 0.012),
        ( s * 0.093, -s * 0.012),
        ( s * 0.280,  s * 0.038),
    ]
    for dx, dy in toe_positions:
        draw.ellipse(
            [cx + dx - toe_rx, toe_base_y + dy - toe_ry,
             cx + dx + toe_rx, toe_base_y + dy + toe_ry],
            fill=color
        )


# ────────────────────────────────────────────────────────────────
# GEOMETRIA — FINDER PATTERNS
# ────────────────────────────────────────────────────────────────

def draw_finder_pattern(draw: ImageDraw.ImageDraw, x0: float, y0: float,
                        s: float):
    """Finder 7×7 com bordas muito suavizadas (r = 1.8 × s)."""
    size7 = 7 * s
    r_out = s * 1.80
    r_gap = s * 1.20
    r_in  = s * 0.75

    draw.rounded_rectangle(
        [x0, y0, x0 + size7, y0 + size7],
        radius=r_out, fill=GREEN_FINDER
    )
    i1 = s
    draw.rounded_rectangle(
        [x0 + i1, y0 + i1, x0 + size7 - i1, y0 + size7 - i1],
        radius=r_gap, fill=BG_WHITE
    )
    i2 = 2 * s
    draw.rounded_rectangle(
        [x0 + i2, y0 + i2, x0 + size7 - i2, y0 + size7 - i2],
        radius=r_in, fill=GREEN_FINDER
    )


def draw_alignment_pattern(draw: ImageDraw.ImageDraw, cx_mod: int, cy_mod: int,
                            s: float, offset: float):
    """Alignment 5×5 estilizado."""
    x0    = offset + (cx_mod - 2) * s
    y0    = offset + (cy_mod - 2) * s
    size5 = 5 * s
    r     = s * 1.0

    draw.rounded_rectangle([x0, y0, x0 + size5, y0 + size5],
                            radius=r, fill=GREEN_FINDER)
    i1 = s
    draw.rounded_rectangle(
        [x0 + i1, y0 + i1, x0 + size5 - i1, y0 + size5 - i1],
        radius=r * 0.55, fill=BG_WHITE
    )
    i2 = 2 * s
    draw.rounded_rectangle(
        [x0 + i2, y0 + i2, x0 + size5 - i2, y0 + size5 - i2],
        radius=r * 0.30, fill=GREEN_FINDER
    )


# ────────────────────────────────────────────────────────────────
# LOGO CENTRAL — CROP + REDIMENSIONAMENTO PROPORCIONAL
# ────────────────────────────────────────────────────────────────

def prepare_logo(logo_path: str, target_px: int) -> Image.Image:
    """
    Recorta a logo completa (símbolo + texto 'Curicas Pet') removendo
    apenas as barras decorativas e a borda opaca da imagem.

    Mapeamento do arquivo logo-curicas-pet.png (660×658px):
      Borda/moldura preta superior : y = 0..65
      Barra verde superior          : y = 105..130
      Símbolo das patinhas          : y = 188..360
      Texto 'Curicas Pet'           : y = 375..510
      Barra verde inferior          : y = 525..545
      Borda/moldura preta inferior  : y = 600..658

    Região útil: y = 188..510 (símbolo + texto completos)

    A proporção original é mantida — a imagem é ajustada com
    aspect-fit dentro do círculo da quiet zone.
    """
    img = Image.open(logo_path).convert('RGBA')
    w, h = img.size   # 660 × 658

    # Crop PRECISO: remove barras pretas superior/inferior + bordas laterais.
    # Coordenadas mapeadas pixel-a-pixel:
    #   Bordas pretas laterais : x=0..12 e x=645..659
    #   Conteúdo útil horizontal: x=13..644
    #   Barra verde + símbolo : y_top  = 184  (início do símbolo)
    #   Texto completo         : y_bot  = 512  (fim de "DOG WALKER • PET SITTER")
    x_left   = 13
    x_right  = 644
    crop_top = int(h * 0.280)   # ~184px
    crop_bot = int(h * 0.778)   # ~512px

    icon = img.crop((x_left, crop_top, x_right, crop_bot))


    # A logo é mais alta que larga no conteúdo útil —
    # redimensionar com aspect-fit: deixar a maior dimensão = target_px * 0.92
    iw, ih  = icon.size
    scale   = (target_px * 0.92) / max(iw, ih)
    new_w   = int(iw * scale)
    new_h   = int(ih * scale)
    icon    = icon.resize((new_w, new_h), Image.LANCZOS)

    # Canvas circular branco de target_px
    result  = Image.new('RGBA', (target_px, target_px), (255, 255, 255, 0))
    bg_draw = ImageDraw.Draw(result)
    bg_draw.ellipse([0, 0, target_px - 1, target_px - 1],
                    fill=(255, 255, 255, 255))

    # Centralizar a logo (aspect-fit) no círculo
    px = (target_px - new_w) // 2
    py = (target_px - new_h) // 2
    result.paste(icon, (px, py), icon)

    # Máscara circular final com anti-aliasing (4× supersampling)
    m  = target_px * 4
    mk = Image.new('L', (m, m), 0)
    ImageDraw.Draw(mk).ellipse([0, 0, m, m], fill=255)
    mk = mk.resize((target_px, target_px), Image.LANCZOS)

    final = Image.new('RGBA', (target_px, target_px), (255, 255, 255, 0))
    final.paste(result, (0, 0), mk)
    return final


# ────────────────────────────────────────────────────────────────
# PIPELINE PRINCIPAL
# ────────────────────────────────────────────────────────────────

def generate_premium_qr(url: str, output_path: str, label: str,
                        size: int = QR_DEFAULT):
    print(f"  Gerando : {label}")
    print(f"  URL     : {url}")
    print(f"  Resolução: {size}×{size}px @ 300 DPI")

    # 1. Matriz QR
    matrix = generate_qr_matrix(url)
    n      = len(matrix)
    print(f"  Módulos : {n}×{n} (Version {(n - 17) // 4})")

    # 2. Dimensionamento de módulos
    total  = n + QUIET_MARGIN * 2
    s      = size / total              # px por módulo
    offset = QUIET_MARGIN * s

    # 3. Canvas branco
    img  = Image.new('RGB', (size, size), BG_WHITE)
    draw = ImageDraw.Draw(img)

    # 4. Zona central para a logo
    # Diâmetro: 30% de n em módulos → ~17% de área (máximo seguro = 30%)
    fav_diam   = int(n * 0.30) | 1   # força ímpar
    fav_center = n // 2
    fav_half   = fav_diam // 2
    # Quiet zone: +2.2 módulos de raio além da logo
    qz_radius  = fav_half + 2.2

    # 5. Módulos de dados como patinhas
    for row in range(n):
        for col in range(n):
            if is_finder(row, col, n):    continue
            if is_alignment(row, col, n): continue
            dist = math.hypot(row - fav_center, col - fav_center)
            if dist <= qz_radius:         continue
            if matrix[row][col]:
                cx = offset + col * s + s / 2
                cy = offset + row * s + s / 2
                draw_paw(draw, cx, cy, s, GREEN_DATA)

    # 6. Três finder patterns (cantos)
    draw_finder_pattern(draw, offset,                       offset,                 s)
    draw_finder_pattern(draw, offset + (n - 7) * s,        offset,                 s)
    draw_finder_pattern(draw, offset,                       offset + (n - 7) * s,  s)

    # 7. Alignment pattern (versão >= 2)
    if n > 21:
        ac = n - 7 - 2
        draw_alignment_pattern(draw, ac, ac, s, offset)

    # 8. Quiet zone circular branca (área de escape ao redor da logo)
    qz_px = int(qz_radius * 2 * s) + 6   # +3px de folga em cada lado
    qz    = Image.new('RGBA', (qz_px, qz_px), (255, 255, 255, 0))
    ImageDraw.Draw(qz).ellipse([0, 0, qz_px, qz_px],
                                fill=(255, 255, 255, 255))
    qz_x = size // 2 - qz_px // 2
    qz_y = size // 2 - qz_px // 2
    img.paste(qz.convert('RGB'), (qz_x, qz_y), qz)

    # 9. Logo centralizada (sobre a quiet zone)
    logo_px  = int(fav_diam * s)
    logo_img = prepare_logo(LOGO, logo_px)
    lx = size // 2 - logo_px // 2
    ly = size // 2 - logo_px // 2
    img.paste(logo_img.convert('RGB'), (lx, ly), logo_img)

    # 10. Salvar PNG
    img.save(output_path, 'PNG', dpi=(300, 300), optimize=True)
    kb = os.path.getsize(output_path) / 1024
    print(f"  ✅ Salvo : {os.path.basename(output_path)} ({kb:.0f} KB)\n")


# ────────────────────────────────────────────────────────────────
# ENTRY POINT
# ────────────────────────────────────────────────────────────────

def main():
    os.makedirs(OUTPUT_DIR, exist_ok=True)

    print("=" * 60)
    print("  GERADOR DE QR CODES PREMIUM v4.0 — CURICAS PET")
    print("=" * 60)
    print("  Logo    : completa (símbolo + texto), proporção preservada")
    print("  Módulos : micro-patinhas #25D366 (verde WhatsApp)")
    print("  Finders : #2D6A0A, arredondados (r=1.8×módulo)")
    print("  QZ      : círculo branco limpo ao redor da logo")
    print("  Redundância: Nível H (30%)")
    print(f"  Saída   : {OUTPUT_DIR}")
    print("=" * 60)
    print()

    for c in CAMPAIGNS:
        out  = os.path.join(OUTPUT_DIR, c['file'])
        size = c.get('size', QR_DEFAULT)
        generate_premium_qr(c['url'], out, c['label'], size)

    print("🎉 Todos os arquivos gerados para gráfica!")
    print(f"   Pasta: {OUTPUT_DIR}")


if __name__ == '__main__':
    main()
