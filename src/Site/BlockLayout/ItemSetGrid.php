<?php
namespace ExtraBlocks\Site\BlockLayout;

use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SiteRepresentation;
use Laminas\View\Renderer\PhpRenderer;

class ItemSetGrid extends AbstractBlockLayout
{
    public function getLabel()
    {
        return 'Item Set Grid';
    }

    public function prepareForm(PhpRenderer $view)
    {
        $view->headScript()->appendFile($view->assetUrl('js/load-items-or-item-sets.js', 'ExtraBlocks'));
    }

    public function form(
        PhpRenderer $view,
        SiteRepresentation $site,
        SitePageRepresentation $page = null,
        SitePageBlockRepresentation $block = null
    ) {
        $data = $block ? $block->data() : [];

        $itemSetId         = $data['item_set_id'] ?? '';
        $itemSetName       = htmlspecialchars($data['item_set_name'] ?? '');
        $perPage           = htmlspecialchars($data['per_page'] ?? '24');
        $sortBy            = $data['sort_by'] ?? 'title';
        $itemWidth         = htmlspecialchars($data['item_width'] ?? '330px');
        $itemHeight        = htmlspecialchars($data['item_height'] ?? '400px');
        $borderColor       = htmlspecialchars($data['border_color'] ?? '#ff0000');
        $borderTransparent = !empty($data['border_transparent']) ? ' checked' : '';
        $borderRadius      = htmlspecialchars($data['border_radius'] ?? '25px');

        $itemSetSidebarUrl = $view->url('admin/default', [
            'controller' => 'item-set',
            'action'     => 'sidebar-select',
        ]);

        $mode = $data['mode'] ?? 'item_set';
        $itemSets = $data['item_sets'] ?? [];
        $manualItems = $data['manual_items'] ?? [];
        $manualCount = (int) ($data['manual_count'] ?? 4);

        $html = '<div class="item-set-grid-form">';

        // Mode selector
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>' . $view->translate('Display mode') . '</label></div>';
        $html .= '<div class="inputs"><select name="o:block[__blockIndex__][o:data][mode]" class="item-set-grid-mode">';
        $html .= '<option value="item_set"' . ($mode === 'item_set' ? ' selected' : '') . '>' . $view->translate('Item Sets') . '</option>';
        $html .= '<option value="manual"' . ($mode === 'manual' ? ' selected' : '') . '>' . $view->translate('Manual selection') . '</option>';
        $html .= '</select></div></div>';

        // ---- Item Set mode fields ----
        $html .= '<div class="item-set-grid-mode-fields item-set-mode"' . ($mode !== 'item_set' ? ' style="display:none"' : '') . '>';

        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>' . $view->translate('Item Sets') . '</label></div>';
        $html .= '<div class="inputs">';
        $html .= '<div class="item-set-grid-list">';

        foreach ($itemSets as $idx => $itemSet) {
            $isId   = htmlspecialchars($itemSet['id'] ?? '');
            $isName = htmlspecialchars($itemSet['name'] ?? '');
            $html .= '<div class="item-set-grid-list-row" style="display:flex;align-items:center;gap:10px;margin-bottom:5px;">';
            $html .= '<span class="item-set-grid-list-name">' . $isName . '</span>';
            $html .= '<input type="hidden" name="o:block[__blockIndex__][o:data][item_sets][' . $idx . '][id]" value="' . $isId . '">';
            $html .= '<input type="hidden" name="o:block[__blockIndex__][o:data][item_sets][' . $idx . '][name]" value="' . $isName . '">';
            $html .= '<a href="#" class="item-set-grid-remove button alert" style="flex-shrink:0;">' . $view->translate('Remove') . '</a>';
            $html .= '</div>';
        }

        $html .= '</div>'; // .item-set-grid-list
        $html .= '<a href="#" class="siteblocks-item-set-select button" data-sidebar-url="' . htmlspecialchars($itemSetSidebarUrl) . '" style="margin-top:5px;">' . $view->translate('Add item set') . '</a>';
        $html .= '</div></div>';

        $html .= '</div>'; // .item-set-mode

        // ---- Manual mode fields ----
        $html .= '<div class="item-set-grid-mode-fields manual-mode"' . ($mode !== 'manual' ? ' style="display:none"' : '') . '>';

        // Manual count
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>' . $view->translate('Number of items') . '</label></div>';
        $html .= '<div class="inputs"><input type="number" min="1" max="100" class="item-set-grid-manual-count" name="o:block[__blockIndex__][o:data][manual_count]" value="' . $manualCount . '"></div>';
        $html .= '</div>';

        // Manual item/itemset pickers
        $html .= '<div class="item-set-grid-manual-list">';

        $itemSidebarUrl = $view->url('admin/default', ['controller' => 'item', 'action' => 'sidebar-select']);

        for ($i = 0; $i < max($manualCount, count($manualItems)); $i++) {
            $mId     = htmlspecialchars($manualItems[$i]['id'] ?? '');
            $mName   = htmlspecialchars($manualItems[$i]['name'] ?? '');
            $display = $i < $manualCount ? '' : ' style="display:none"';
        
            $html .= '<div class="item-set-grid-manual-row"' . $display . ' style="border:1px solid #ddd;padding:10px;margin-bottom:10px;border-radius:4px;">';
            $html .= '<strong>' . $view->translate('Slot') . ' ' . ($i + 1) . '</strong>';
        
            // Hidden type field — always items
            $html .= '<input type="hidden" name="o:block[__blockIndex__][o:data][manual_items][' . $i . '][type]" value="items">';
        
            // Resource picker
            $html .= '<div class="field">';
            $html .= '<div class="field-meta"><label>' . $view->translate('Item') . '</label></div>';
            $html .= '<div class="inputs"><div class="siteblocks-asset-picker">';
        
            $html .= '<input type="hidden" class="siteblocks-asset-id" name="o:block[__blockIndex__][o:data][manual_items][' . $i . '][id]" value="' . $mId . '">';
            $html .= '<input type="hidden" class="siteblocks-asset-name" name="o:block[__blockIndex__][o:data][manual_items][' . $i . '][name]" value="' . $mName . '">';
        
            $previewStyle = $mId ? '' : ' style="display:none"';
            $html .= '<div class="siteblocks-asset-preview"' . $previewStyle . '>';
            $html .= '<span class="siteblocks-preview-name">' . $mName . '</span>';
            $html .= '</div>';
        
            $clearStyle = $mId ? '' : ' style="display:none"';
            $html .= '<a href="#" class="siteblocks-asset-clear button alert"' . $clearStyle . '>' . $view->translate('Clear') . '</a> ';
            $html .= '<a href="#" class="item-set-grid-manual-select button" data-item-sidebar-url="' . htmlspecialchars($itemSidebarUrl) . '">' . $view->translate('Select') . '</a>';
        
            $html .= '</div></div></div>';
            $html .= '</div>'; // .item-set-grid-manual-row
        }

        $html .= '</div>'; // .item-set-grid-manual-list
        $html .= '</div>'; // .manual-mode

        // ---- Shared fields ----
        // Per page
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>' . $view->translate('Items per page') . '</label></div>';
        $html .= '<div class="inputs"><input type="number" min="1" max="100" name="o:block[__blockIndex__][o:data][per_page]" value="' . $perPage . '"></div>';
        $html .= '</div>';

        // Sort by
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>' . $view->translate('Default sort') . '</label></div>';
        $html .= '<div class="inputs"><select name="o:block[__blockIndex__][o:data][sort_by]">';
        foreach ([
            'title'           => $view->translate('Title'),
            'created'         => $view->translate('Date'),
            'dcterms:creator' => $view->translate('Author/Creator'),
        ] as $value => $label) {
            $selected = $sortBy === $value ? ' selected' : '';
            $html .= "<option value=\"{$value}\"{$selected}>{$label}</option>";
        }
        $html .= '</select></div></div>';

        // Width
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>' . $view->translate('Block Width (CSS value e.g. 250px, 20vw)') . '</label></div>';
        $html .= '<div class="inputs"><input type="text" name="o:block[__blockIndex__][o:data][item_width]" value="' . $itemWidth . '"></div>';
        $html .= '</div>';

        // Height
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>' . $view->translate('Block Height (CSS value e.g. 300px, 30vh)') . '</label></div>';
        $html .= '<div class="inputs"><input type="text" name="o:block[__blockIndex__][o:data][item_height]" value="' . $itemHeight . '"></div>';
        $html .= '</div>';

        // Border radius
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>' . $view->translate('Border radius (CSS value e.g. 25px, 0)') . '</label></div>';
        $html .= '<div class="inputs"><input type="text" name="o:block[__blockIndex__][o:data][border_radius]" value="' . $borderRadius . '"></div>';
        $html .= '</div>';

        // Border color
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>' . $view->translate('Border Color') . '</label></div>';
        $html .= '<div class="inputs" style="display:flex;align-items:center;gap:10px;">';
        $html .= '<input type="color" name="o:block[__blockIndex__][o:data][border_color]" value="' . $borderColor . '"' . (!empty($data['border_transparent']) ? ' disabled' : '') . '>';
        $html .= '<label style="display:flex;align-items:center;gap:5px;">';
        $html .= '<input type="checkbox" class="siteblocks-transparent-check" name="o:block[__blockIndex__][o:data][border_transparent]" value="1"' . $borderTransparent . '> ' . $view->translate('Transparent');
        $html .= '</label>';
        $html .= '</div></div>';

        $html .= '</div>'; // .item-set-grid-form

        return $html;
            }

public function render(PhpRenderer $view, SitePageBlockRepresentation $block)
{
    $data             = $block->data();
    $mode             = $data['mode'] ?? 'item_set';
    $perPage          = (int) ($data['per_page'] ?? 12);
    $sortBy           = $data['sort_by'] ?? 'title';
    $blockId          = $block->id();
    $page             = (int) ($view->params()->fromQuery('grid_page_' . $blockId, 1));
    $currentSortBy    = $view->params()->fromQuery('sort_by_' . $blockId, $sortBy);
    $currentSortOrder = $view->params()->fromQuery('sort_order_' . $blockId, 'asc');
    $itemWidth        = $data['item_width'] ?? '330px';
    $itemHeight       = $data['item_height'] ?? '400px';
    $borderRadius     = $data['border_radius'] ?? '25px';
    $borderColor      = !empty($data['border_transparent']) ? 'transparent' : ($data['border_color'] ?? '#ff0000');

    $items      = [];
    $totalCount = 0;
    $totalPages = 1;

    if ($mode === 'manual') {
        $manualItems = $data['manual_items'] ?? [];
        $manualCount = (int) ($data['manual_count'] ?? 4);
        $manualItems = array_slice($manualItems, 0, $manualCount);

        $allItems = [];
        foreach ($manualItems as $manualItem) {
            $id   = $manualItem['id'] ?? null;
            $type = $manualItem['type'] ?? 'items';
            if (!$id) continue;
            try {
                $resource   = $view->api()->read($type, (int) $id)->getContent();
                $allItems[] = $resource;
            } catch (\Exception $e) {
                // Skip missing resources
            }
        }

        $totalCount = count($allItems);
        $totalPages = (int) ceil($totalCount / $perPage) ?: 1;
        $offset     = ($page - 1) * $perPage;
        $items      = array_slice($allItems, $offset, $perPage);

    } else {
        $itemSets   = $data['item_sets'] ?? [];
        $itemSetIds = array_values(array_filter(array_column($itemSets, 'id')));

        // Fall back to legacy single item_set_id
        if (empty($itemSetIds) && !empty($data['item_set_id'])) {
            $itemSetIds = [$data['item_set_id']];
        }

        if (empty($itemSetIds)) {
            return '<p>' . $view->translate('No item set selected.') . '</p>';
        }

        $itemSetParam = count($itemSetIds) === 1 ? (int) $itemSetIds[0] : array_map('intval', $itemSetIds);

        try {
            $response   = $view->api()->search('items', [
                'item_set_id' => $itemSetParam,
                'page'        => $page,
                'per_page'    => $perPage,
                'sort_by'     => $currentSortBy,
                'sort_order'  => $currentSortOrder,
            ]);
            $items      = $response->getContent();
            $totalCount = $response->getTotalResults();
            $totalPages = (int) ceil($totalCount / $perPage) ?: 1;
        } catch (\Exception $e) {
            return '<p>' . $view->translate('Could not load items.') . '</p>';
        }
    }

    return $view->partial('common/block-layout/item-set-grid', [
        'items'         => $items,
        'blockId'       => $blockId,
        'page'          => $page,
        'totalPages'    => $totalPages,
        'perPage'       => $perPage,
        'sortBy'        => $currentSortBy,
        'defaultSortBy' => $sortBy,
        'itemWidth'     => $itemWidth,
        'itemHeight'    => $itemHeight,
        'borderRadius'  => $borderRadius,
        'borderColor'   => $borderColor,
        'totalCount'    => $totalCount,
        'sortOrder'     => $currentSortOrder,
    ]);
}

    public function prepareRender(PhpRenderer $view)
    {
        $view->headStyle()->appendStyle(file_get_contents(__DIR__ . '/../../../asset/css/item-set-grid.css'));
    }
}