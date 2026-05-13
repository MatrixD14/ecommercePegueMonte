<?php
return [
    'produtos' => [
        'tabela' => 'produtos',
        'colunas' => [
            'id' => [
                'type' => "number",
                "primary" => "true",
            ],
            "nome" => [
                'type' => "text",
                'label' => 'Nome do Produto',
            ],
            'descricao' => [
                'type' => 'text',
                'label' => 'Descrição',
            ],
            'preco' => [
                'type' => "number",
                'label' => "Preço",
                'step' => '0.01',
            ],
            'estoque' => [
                'type' => "number",
                'label' => 'Estoque',
            ],
            'img' => [
                'type' => 'file',
                'label' => 'Imagem',
                'format_type' => ['jpeg', 'jpg', 'png', 'webp'],
            ],
            'categoria_id' => [
                'type' => 'select',
                'label' => 'Categoria',
                'relation' => [
                    'tabela' => 'categorias',
                    'coluna' => 'nome',
                    'value' => 'id'
                ]
            ]
        ]
    ],
    'categorias' => [
        'tabela' => "categorias",
        'colunas' => [
            'id' => [
                'type' => 'number',
                'primary' => 'true'
            ],
            'nome' => [
                'type' => 'text',
                'label' => "Nome da Categoria"
            ]
        ]
    ]
];
