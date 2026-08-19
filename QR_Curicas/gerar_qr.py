import os
import segno
from PIL import Image, ImageDraw

# Configurações de cores oficiais da marca
COR_PONTOS = "#25D366"     # Verde Vibrante
COR_CANTOS = "#263A1A"     # Verde-Escuro Militar (do seu anexo)
COR_FUNDO = "#FFFFFF"      # Fundo Branco

CAMPAIGNS = {
    "qr-camiseta.png": "https://curicaspet.com.br/camiseta",
    "qr-avaliar.png": "https://curicaspet.com.br/avaliar"
}

def criar_patinha_marcada(cor, tamanho):
    """Desenha uma micro-patinha maior e mais robusta preenchendo melhor o bloco"""
    img = Image.new("RGBA", (tamanho, tamanho), (255, 255, 255, 0))
    draw = ImageDraw.Draw(img)
    
    # Almofada central expandida (preenche melhor o espaço do pixel)
    pad_w = int(tamanho * 0.65)
    pad_h = int(tamanho * 0.52)
    pad_x = (tamanho - pad_w) // 2
    pad_y = int(tamanho * 0.40)
    draw.ellipse([pad_x, pad_y, pad_x + pad_w, pad_y + pad_h], fill=cor)
    
    # 4 Dedinhos (Toes) maiores e mais destacados
    toe_size = int(tamanho * 0.26)
    toes_y = [int(tamanho * 0.18), int(tam  anho * 0.06), int(tamanho * 0.06), int(tamanho * 0.18)]
    toes_x = [int(tamanho * 0.15), int(tamanho * 0.38), int(tamanho * 0.62), int(tamanho * 0.85)]
    
    for x, y in zip(toes_x, toes_y):
        draw.ellipse([x - toe_size//2, y, x + toe_size//2, y + toe_size], fill=cor)
        
    return img

def desenhar_canto_arredondado(draw, x, y, box_real):
    """Desenha o Finder Pattern (quadrado do canto) com as bordas arredondadas orgânicas perfeitas"""
    tamanho_total_canto = 7 * box_real
    
    # 1. Moldura externa escura arredondada
    raio_ext = int(tamanho_total_canto * 0.28)
    draw.rounded_rectangle(
        [x, y, x + tamanho_total_canto, y + tamanho_total_canto],
        radius=raio_ext,
        fill=COR_CANTOS
    )
    
    # 2. Quadrado interno branco arredondado para fazer o vazado
    recuo_vazado = box_real
    raio_vazado = int((tamanho_total_canto - (recuo_vazado * 2)) * 0.25)
    draw.rounded_rectangle(
        [x + recuo_vazado, y + recuo_vazado, x + tamanho_total_canto - recuo_vazado, y + tamanho_total_canto - recuo_vazado],
        radius=raio_vazado,
        fill=COR_FUNDO
    )
    
    # 3. Centro escuro (miolo) arredondado
    recuo_miolo = box_real * 2
    raio_miolo = int((tamanho_total_canto - (recuo_miolo * 2)) * 0.25)
    draw.rounded_rectangle(
        [x + recuo_miolo, y + recuo_miolo, x + tamanho_total_canto - recuo_miolo, y + tamanho_total_canto - recuo_miolo],
        radius=raio_miolo,
        fill=COR_CANTOS
    )

def gerar_qr_premium_arredondado(filename, url):
    logo_path = "favicon-matriz.png"
    if not os.path.exists(logo_path):
        print(f"⚠️ Erro: Certifique-se de que o arquivo '{logo_path}' está na mesma pasta.")
        return

    # 1. Cria a matriz matemática do QR Code com redundância máxima (H)
    qr = segno.make(url, error='H')
    matrix = list(qr.matrix)
    tam_matriz = len(matrix)
    
    box_real = 30  
    borda = 4
    tamanho_total = (tam_matriz + (borda * 2)) * box_real
    
    # 2. CORREÇÃO AQUI: Cria a imagem final branca sem o operador inválido
    img_final = Image.new("RGBA", (tamanho_total, tamanho_total), COR_FUNDO)
    draw_final = ImageDraw.Draw(img_final)
    
    # 3. Cria o carimbo da patinha maior ajustada
    img_patinha = criar_patinha_marcada(COR_PONTOS, box_real)
    
    # 4. Renderiza as patinhas na matriz (pulando a área dos 3 cantos)
    for r, linha in enumerate(matrix):
        for c, bit in enumerate(linha):
            if bit:
                is_finder = (
                    (r < 7 and c < 7) or 
                    (r < 7 and c >= tam_matriz - 7) or 
                    (r >= tam_matriz - 7 and c < 7)
                )
                
                if not is_finder:
                    pos_x = (c + borda) * box_real
                    pos_y = (r + borda) * box_real
                    img_final.paste(img_patinha, (pos_x, pos_y), img_patinha)

    # 5. Desenha os 3 cantos com o arredondamento geométrico perfeito que você aprovou
    desenhar_canto_arredondado(draw_final, borda * box_real, borda * box_real, box_real) # Sup Esq
    desenhar_canto_arredondado(draw_final, (tam_matriz - 7 + borda) * box_real, borda * box_real, box_real) # Sup Dir
    desenhar_canto_arredondado(draw_final, borda * box_real, (tam_matriz - 7 + borda) * box_real, box_real) # Inf Esq

    # 6. Processamento e centralização da Logo
    img_logo = Image.open(logo_path).convert("RGBA")
    tamanho_centro = int(tamanho_total * 0.28)
    img_logo = img_logo.resize((tamanho_centro, tamanho_centro), Image.Resampling.LANCZOS)
    
    # 7. Criação da área branca redonda de proteção (Quiet Zone)
    mascara_branca = Image.new("RGBA", img_final.size, (0, 0, 0, 0))
    draw_centro = ImageDraw.Draw(mascara_branca)
    margem = (tamanho_total - tamanho_centro) // 2
    
    draw_centro.ellipse(
        [margem - 25, margem - 25, margem + tamanho_centro + 25, margem + tamanho_centro + 25], 
        fill=COR_FUNDO
    )
    
    # Une tudo e aplica a logo central
    img_definitiva = Image.alpha_composite(img_final, mascara_branca)
    posicao_logo = ((tamanho_total - tamanho_centro) // 2, (tamanho_total - tamanho_centro) // 2)
    img_definitiva.paste(img_logo, posicao_logo, img_logo)
    
    # Salva a imagem finalizada pronta para uso
    img_definitiva.convert("RGB").save(filename, "PNG")
    print(f"✅ {filename} gerado com patinhas grandes e cantos arredondados!")

# Executa o loop
for arquivo, link in CAMPAIGNS.items():
    gerar_qr_premium_arredondado(arquivo, link)