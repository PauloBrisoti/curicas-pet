"""
=============================================================
  QR Code Estilizado - Curicas Pet
  Gera QR code com módulos em formato de patinha e logo no centro
=============================================================

INSTALAÇÃO (execute uma vez no terminal):
    pip install qrcode[pil] Pillow

USO:
    1. Coloque a logo da Curicas Pet na mesma pasta com o nome: logo_curicas_pet.png
    2. Edite a variável URL abaixo com o seu site
    3. Execute: python gerar_qrcode_curicas_pet.py

=============================================================
"""

import qrcode
from qrcode.image.styledpil import StyledPilImage
from qrcode.image.styles.moduledrawers.pil import (
    RoundedModuleDrawer,
    GappedSquareModuleDrawer,
    CircleModuleDrawer,
)
from qrcode.image.styles.colormasks import SolidFillColorMask
from PIL import Image, ImageDraw, ImageFilter
import math
import os

# ============================================================
#  ⚙️  CONFIGURAÇÕES — edite aqui
# ============================================================
URL          = "https://www.curicaspet.com.br"   # ← seu site aqui
LOGO_PATH    = "/Users/paulobarbosa/Documents/curicaspet/curicas-pet/QR_Curicas/teste/image_9.png"            # ← nome do arquivo da logo
OUTPUT_PATH  = "qrcode_curicas_pet.png"

# Cores (R, G, B)
COR_ESCURA   = (30, 100, 30)    # verde escuro (módulos principais)
COR_MEDIA    = (60, 140, 60)    # verde médio
COR_CLARA    = (120, 180, 60)   # verde claro
FUNDO        = (255, 255, 255)  # branco

# Tamanho final da imagem em pixels
TAMANHO_FINAL = 1200

# Proporção da logo em relação ao QR code (0.22 = 22%)
PROPORCAO_LOGO = 0.22
# ============================================================


def criar_patinha(tamanho=38, cor=(30, 100, 30), variacao=0):
    """
    Desenha uma patinha de cachorro/gato usando formas geométricas simples.
    variacao: 0, 1 ou 2 para pequenas diferenças de estilo (variedade visual)
    """
    img = Image.new("RGBA", (tamanho, tamanho), (0, 0, 0, 0))
    draw = ImageDraw.Draw(img)

    r, g, b = cor
    cor_rgba = (r, g, b, 220)

    t = tamanho

    # Palmeira central (elipse grande na parte inferior)
    palma_x0 = int(t * 0.20)
    palma_y0 = int(t * 0.45)
    palma_x1 = int(t * 0.80)
    palma_y1 = int(t * 0.92)
    draw.ellipse([palma_x0, palma_y0, palma_x1, palma_y1], fill=cor_rgba)

    # Dedos (4 bolinhas na parte superior)
    dedo_r = int(t * 0.12)  # raio de cada dedo

    if variacao == 0:
        posicoes_dedos = [
            (int(t * 0.19), int(t * 0.28)),
            (int(t * 0.38), int(t * 0.18)),
            (int(t * 0.58), int(t * 0.18)),
            (int(t * 0.78), int(t * 0.28)),
        ]
    elif variacao == 1:
        posicoes_dedos = [
            (int(t * 0.16), int(t * 0.30)),
            (int(t * 0.36), int(t * 0.16)),
            (int(t * 0.60), int(t * 0.16)),
            (int(t * 0.80), int(t * 0.30)),
        ]
    else:
        posicoes_dedos = [
            (int(t * 0.20), int(t * 0.26)),
            (int(t * 0.40), int(t * 0.15)),
            (int(t * 0.60), int(t * 0.15)),
            (int(t * 0.76), int(t * 0.28)),
        ]

    for (cx, cy) in posicoes_dedos:
        draw.ellipse(
            [cx - dedo_r, cy - dedo_r, cx + dedo_r, cy + dedo_r],
            fill=cor_rgba,
        )

    return img


def escolher_cor(linha, coluna, total_linhas, total_colunas):
    """Escolhe variação de verde com base na posição para criar gradiente sutil."""
    fator_linha = linha / max(total_linhas - 1, 1)
    fator_col   = coluna / max(total_colunas - 1, 1)
    dist_centro = math.sqrt((fator_linha - 0.5) ** 2 + (fator_col - 0.5) ** 2)

    if dist_centro < 0.2:
        return COR_ESCURA
    elif dist_centro < 0.4:
        return COR_MEDIA
    else:
        return tuple(
            int(COR_MEDIA[i] + (COR_CLARA[i] - COR_MEDIA[i]) * (dist_centro - 0.4) / 0.4)
            for i in range(3)
        )


def gerar_qrcode_patinhas(url, logo_path, output_path):
    print("📐 Gerando dados do QR code...")

    qr = qrcode.QRCode(
        version=None,
        error_correction=qrcode.constants.ERROR_CORRECT_H,  # 30% correção (espaço pra logo)
        box_size=10,
        border=4,
    )
    qr.add_data(url)
    qr.make(fit=True)

    matriz = qr.get_matrix()
    n = len(matriz)
    print(f"   Matriz: {n}x{n} módulos")

    # Tamanho de cada módulo em pixels
    modulo_px = TAMANHO_FINAL // (n + 8)  # +8 para margem
    margem_px = modulo_px * 4

    canvas_size = n * modulo_px + 2 * margem_px
    imagem = Image.new("RGBA", (canvas_size, canvas_size), FUNDO + (255,))

    print("🐾 Desenhando módulos como patinhas...")

    for linha in range(n):
        for coluna in range(n):
            if not matriz[linha][coluna]:
                continue  # módulo apagado — pula

            x = margem_px + coluna * modulo_px
            y = margem_px + linha * modulo_px

            cor    = escolher_cor(linha, coluna, n, n)
            var    = (linha * 3 + coluna * 7) % 3   # variação de forma
            patinha = criar_patinha(tamanho=modulo_px, cor=cor, variacao=var)

            # Rotação aleatória determinística para parecer natural
            angulo = ((linha * 13 + coluna * 17) % 5) * 18 - 36  # -36 a +36 graus
            patinha = patinha.rotate(angulo, resample=Image.BICUBIC, expand=False)

            imagem.paste(patinha, (x, y), patinha)

    # ── Adicionar logo no centro ──────────────────────────────
    if os.path.exists(logo_path):
        print("🖼️  Inserindo logo no centro...")

        logo = Image.open(logo_path).convert("RGBA")
        logo_max = int(canvas_size * PROPORCAO_LOGO)

        # Manter proporção
        logo.thumbnail((logo_max, logo_max), Image.LANCZOS)
        lw, lh = logo.size

        # Fundo branco arredondado atrás da logo
        padding = int(logo_max * 0.12)
        fundo_logo = Image.new("RGBA", (lw + padding * 2, lh + padding * 2), (0, 0, 0, 0))
        draw_fundo = ImageDraw.Draw(fundo_logo)
        raio = int((lw + padding * 2) * 0.15)
        draw_fundo.rounded_rectangle(
            [0, 0, lw + padding * 2 - 1, lh + padding * 2 - 1],
            radius=raio,
            fill=(255, 255, 255, 240),
        )

        # Sombra suave
        sombra = fundo_logo.filter(ImageFilter.GaussianBlur(radius=4))
        sx = (canvas_size - fundo_logo.width) // 2 + 4
        sy = (canvas_size - fundo_logo.height) // 2 + 4
        imagem.paste(sombra, (sx, sy), sombra)

        # Colar fundo branco
        fx = (canvas_size - fundo_logo.width) // 2
        fy = (canvas_size - fundo_logo.height) // 2
        imagem.paste(fundo_logo, (fx, fy), fundo_logo)

        # Colar logo
        lx = fx + padding
        ly = fy + padding
        imagem.paste(logo, (lx, ly), logo)
    else:
        print(f"⚠️  Logo não encontrada em '{logo_path}' — QR gerado sem logo.")
        print("   Coloque a logo na mesma pasta e rode novamente.")

    # ── Converter para RGB e salvar ───────────────────────────
    imagem_rgb = Image.new("RGB", imagem.size, FUNDO)
    imagem_rgb.paste(imagem, mask=imagem.split()[3])
    imagem_rgb.save(output_path, "PNG", dpi=(300, 300))

    print(f"\n✅ QR code salvo em: {output_path}")
    print(f"   Resolução: {imagem_rgb.size[0]}x{imagem_rgb.size[1]}px @ 300dpi")

    # ── Verificação rápida de leitura ─────────────────────────
    try:
        from pyzbar.pyzbar import decode as pyz_decode
        qr_check = pyz_decode(imagem_rgb)
        if qr_check:
            print(f"   ✔ QR válido — decodificado: {qr_check[0].data.decode()}")
        else:
            print("   ⚠️  Não foi possível verificar automaticamente — teste com seu celular.")
    except ImportError:
        print("   ℹ️  (instale pyzbar para verificação automática: pip install pyzbar)")


# ── Versão alternativa: usa qrcode.StyledPilImage (mais simples) ──────────────
def gerar_qrcode_estilizado_simples(url, logo_path, output_path):
    """
    Versão alternativa usando a API nativa StyledPilImage do qrcode.
    Produz módulos arredondados com logo no centro — mais rápida, menos detalhada.
    """
    print("📐 Gerando QR estilizado (versão simplificada)...")

    logo = None
    if os.path.exists(logo_path):
        logo = Image.open(logo_path)

    qr = qrcode.QRCode(
        error_correction=qrcode.constants.ERROR_CORRECT_H,
        box_size=15,
        border=4,
    )
    qr.add_data(url)
    qr.make(fit=True)

    img_qr = qr.make_image(
        image_factory=StyledPilImage,
        module_drawer=RoundedModuleDrawer(radius_ratio=0.8),
        color_mask=SolidFillColorMask(
            back_color=FUNDO,
            front_color=COR_ESCURA,
        ),
        embeded_image=logo,
    )

    img_qr.save(output_path)
    print(f"✅ QR code salvo em: {output_path}")


# ── MAIN ──────────────────────────────────────────────────────
if __name__ == "__main__":
    print("=" * 55)
    print("  🐾  QR Code Curicas Pet — Gerador")
    print("=" * 55)
    print(f"  URL  : {URL}")
    print(f"  Logo : {LOGO_PATH}")
    print(f"  Saída: {OUTPUT_PATH}")
    print("=" * 55 + "\n")

    # Modo principal: patinhas desenhadas individualmente
    gerar_qrcode_patinhas(URL, LOGO_PATH, OUTPUT_PATH)

    # Descomente abaixo para a versão mais simples (módulos arredondados):
    # gerar_qrcode_estilizado_simples(URL, LOGO_PATH, "qrcode_simples.png")