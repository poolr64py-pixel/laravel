#!/usr/bin/env python3
import json
import sys
from deep_translator import GoogleTranslator

def translate_json_file(input_file, output_file):
    print(f"Traduzindo {input_file}...")
    
    # Ler o arquivo JSON
    with open(input_file, 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    translator = GoogleTranslator(source='en', target='pt')
    translated_data = {}
    total = len(data)
    count = 0
    
    # Traduzir cada valor
    for key, value in data.items():
        count += 1
        try:
            # Traduzir o valor
            translated_value = translator.translate(value)
            translated_data[key] = translated_value
            
            if count % 10 == 0:
                print(f"Progresso: {count}/{total} ({int(count/total*100)}%)")
                
        except Exception as e:
            print(f"Erro ao traduzir '{key}': {e}")
            translated_data[key] = value  # Manter original se der erro
    
    # Salvar o arquivo traduzido
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(translated_data, f, ensure_ascii=False, indent=2)
    
    print(f"✓ Arquivo salvo: {output_file}")

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Uso: python3 translate_json.py <arquivo_entrada> <arquivo_saida>")
        sys.exit(1)
    
    translate_json_file(sys.argv[1], sys.argv[2])
