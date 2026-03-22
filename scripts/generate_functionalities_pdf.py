from datetime import datetime
from pathlib import Path
import textwrap


ROOT = Path(__file__).resolve().parents[1]
SOURCE = ROOT / 'FUNCIONALIDADES_APLICACION_RENTCAR.md'
OUTPUT = ROOT / 'FUNCIONALIDADES_APLICACION_RENTCAR.pdf'

PAGE_W = 595
PAGE_H = 842
CONTENT_X = 56
TOP_Y = 730
BOTTOM_Y = 64

NAVY = (0.09, 0.20, 0.36)
SKY = (0.21, 0.53, 0.78)
GOLD = (0.79, 0.63, 0.24)
SLATE = (0.23, 0.27, 0.33)
MUTED = (0.46, 0.50, 0.56)
LIGHT = (0.96, 0.97, 0.98)
WHITE = (1.0, 1.0, 1.0)


def color_fill(color):
    return f'{color[0]:.3f} {color[1]:.3f} {color[2]:.3f} rg'


def color_stroke(color):
    return f'{color[0]:.3f} {color[1]:.3f} {color[2]:.3f} RG'


def escape_pdf_text(value: str) -> str:
    return value.replace('\\', '\\\\').replace('(', '\\(').replace(')', '\\)')


def sanitize_text(value: str) -> str:
    return escape_pdf_text(value.encode('latin-1', 'replace').decode('latin-1'))


def wrap_line(value: str, width: int):
    return textwrap.wrap(value, width=width) or ['']


def normalize_lines(text: str):
    tokens = []

    for raw in text.splitlines():
        line = raw.rstrip()

        if not line:
            tokens.append({'kind': 'blank', 'text': ''})
            continue

        if line.startswith('# '):
            tokens.append({'kind': 'h1', 'text': line[2:].strip()})
            continue

        if line.startswith('## '):
            tokens.append({'kind': 'h2', 'text': line[3:].strip()})
            continue

        if line.startswith('### '):
            tokens.append({'kind': 'h3', 'text': line[4:].strip()})
            continue

        if line.startswith('- '):
            wrapped = wrap_line(line[2:].strip(), 74)
            for index, part in enumerate(wrapped):
                tokens.append({
                    'kind': 'bullet',
                    'text': part,
                    'first': index == 0,
                })
            continue

        for part in wrap_line(line, 82):
            tokens.append({'kind': 'body', 'text': part})

    return tokens


def token_height(token):
    return {
        'blank': 10,
        'h1': 32,
        'h2': 26,
        'h3': 20,
        'bullet': 15,
        'body': 15,
    }[token['kind']]


def paginate(tokens):
    pages = []
    current = []
    y = TOP_Y

    for token in tokens:
        height = token_height(token)

        if y - height < BOTTOM_Y:
            pages.append(current)
            current = []
            y = TOP_Y

        current.append((token, y))
        y -= height

    if current:
        pages.append(current)

    return pages


def draw_rect(x, y, w, h, fill):
    return f'q {color_fill(fill)} {x:.2f} {y:.2f} {w:.2f} {h:.2f} re f Q'


def draw_line(x1, y1, x2, y2, stroke, width=1):
    return f'q {color_stroke(stroke)} {width:.2f} w {x1:.2f} {y1:.2f} m {x2:.2f} {y2:.2f} l S Q'


def draw_text(x, y, text, font='F1', size=11, color=SLATE):
    safe = sanitize_text(text)
    return '\n'.join([
        'BT',
        color_fill(color),
        f'/{font} {size} Tf',
        f'1 0 0 1 {x:.2f} {y:.2f} Tm',
        f'({safe}) Tj',
        'ET',
    ])


def build_cover_page(title):
    today = datetime.now().strftime('%d/%m/%Y')
    return [
        draw_rect(0, 0, PAGE_W, PAGE_H, LIGHT),
        draw_rect(0, PAGE_H - 210, PAGE_W, 210, NAVY),
        draw_rect(0, PAGE_H - 218, PAGE_W, 8, SKY),
        draw_rect(56, 438, PAGE_W - 112, 150, WHITE),
        draw_line(56, 586, PAGE_W - 56, 586, GOLD, 2.5),
        draw_line(56, 438, PAGE_W - 56, 438, SKY, 1.2),
        draw_text(56, 742, 'RENTCAR', 'F2', 18, WHITE),
        draw_text(56, 702, title, 'F2', 28, WHITE),
        draw_text(56, 674, 'Documento funcional institucional', 'F1', 14, (0.86, 0.91, 0.97)),
        draw_text(56, 642, 'Resumen integral de modulos, roles, relaciones y capacidades operativas del sistema.', 'F1', 11, (0.80, 0.87, 0.95)),
        draw_text(80, 548, 'Alcance del documento', 'F2', 16, NAVY),
        draw_text(80, 522, 'Este PDF describe las funcionalidades visibles y operativas de la aplicacion,', 'F1', 11, SLATE),
        draw_text(80, 506, 'incluyendo gestion administrativa, portal del conductor, permisos y flujo de datos.', 'F1', 11, SLATE),
        draw_text(80, 478, f'Fecha de emision: {today}', 'F2', 11, SKY),
        draw_text(56, 86, 'RentCar · Documento de referencia funcional', 'F1', 10, MUTED),
    ]


def build_content_page(page_number, total_pages, items):
    commands = [
        draw_rect(0, PAGE_H - 48, PAGE_W, 48, NAVY),
        draw_rect(0, PAGE_H - 52, PAGE_W, 4, SKY),
        draw_text(56, PAGE_H - 32, 'RentCar · Funcionalidades de la aplicacion', 'F2', 12, WHITE),
        draw_line(56, 52, PAGE_W - 56, 52, (0.82, 0.85, 0.88), 0.8),
        draw_text(56, 34, 'Documento funcional institucional', 'F1', 9, MUTED),
        draw_text(PAGE_W - 110, 34, f'Pagina {page_number}/{total_pages}', 'F2', 9, MUTED),
    ]

    for token, y in items:
        kind = token['kind']
        text = token['text']

        if kind == 'blank':
            continue

        if kind == 'h1':
            commands.append(draw_text(CONTENT_X, y, text, 'F2', 20, NAVY))
            commands.append(draw_line(CONTENT_X, y - 6, CONTENT_X + 220, y - 6, GOLD, 2.0))
            continue

        if kind == 'h2':
            commands.append(draw_text(CONTENT_X, y, text, 'F2', 14, NAVY))
            commands.append(draw_line(CONTENT_X, y - 4, CONTENT_X + 180, y - 4, SKY, 1.2))
            continue

        if kind == 'h3':
            commands.append(draw_text(CONTENT_X, y, text, 'F2', 11, SKY))
            continue

        if kind == 'bullet':
            if token.get('first', False):
                commands.append(draw_text(CONTENT_X + 2, y, '-', 'F2', 11, GOLD))
            commands.append(draw_text(CONTENT_X + 14, y, text, 'F1', 10.8, SLATE))
            continue

        commands.append(draw_text(CONTENT_X, y, text, 'F1', 10.8, SLATE))

    return commands


def add_object(objects, data):
    objects.append(data)
    return len(objects)


def build_pdf(command_pages):
    objects = []
    font_regular = add_object(objects, '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>')
    font_bold = add_object(objects, '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>')

    page_ids = []
    content_ids = []

    for commands in command_pages:
        stream = '\n'.join(commands)
        content_id = add_object(objects, f'<< /Length {len(stream.encode("latin-1"))} >>\nstream\n{stream}\nendstream')
        content_ids.append(content_id)
        page_ids.append(add_object(objects, None))

    kids = '[ ' + ' '.join(f'{page_id} 0 R' for page_id in page_ids) + ' ]'
    pages_id = add_object(objects, f'<< /Type /Pages /Kids {kids} /Count {len(page_ids)} /MediaBox [0 0 {PAGE_W} {PAGE_H}] >>')

    for index, page_id in enumerate(page_ids):
        objects[page_id - 1] = (
            f'<< /Type /Page /Parent {pages_id} 0 R /Resources << /Font << /F1 {font_regular} 0 R /F2 {font_bold} 0 R >> >> '
            f'/Contents {content_ids[index]} 0 R >>'
        )

    catalog_id = add_object(objects, f'<< /Type /Catalog /Pages {pages_id} 0 R >>')

    pdf = bytearray(b'%PDF-1.4\n%\xe2\xe3\xcf\xd3\n')
    offsets = [0]

    for index, obj in enumerate(objects, start=1):
        offsets.append(len(pdf))
        pdf.extend(f'{index} 0 obj\n'.encode('latin-1'))
        pdf.extend(obj.encode('latin-1'))
        pdf.extend(b'\nendobj\n')

    xref_pos = len(pdf)
    pdf.extend(f'xref\n0 {len(objects) + 1}\n'.encode('latin-1'))
    pdf.extend(b'0000000000 65535 f \n')

    for offset in offsets[1:]:
        pdf.extend(f'{offset:010d} 00000 n \n'.encode('latin-1'))

    pdf.extend(f'trailer\n<< /Size {len(objects) + 1} /Root {catalog_id} 0 R >>\nstartxref\n{xref_pos}\n%%EOF\n'.encode('latin-1'))
    return pdf


def main():
    text = SOURCE.read_text(encoding='utf-8')
    tokens = normalize_lines(text)
    pages = paginate(tokens)

    title = 'Funcionalidades de la Aplicacion RentCar'
    total_pages = len(pages)
    content_pages = [
        build_content_page(index + 1, total_pages, page)
        for index, page in enumerate(pages)
    ]
    command_pages = [build_cover_page(title), *content_pages]

    pdf = build_pdf(command_pages)
    OUTPUT.write_bytes(pdf)
    print(str(OUTPUT))


if __name__ == '__main__':
    main()