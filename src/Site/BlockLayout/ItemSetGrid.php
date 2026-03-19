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
        return 'Item Set Grid'; // @translate
    }

    public function prepareForm(PhpRenderer $view)
    {
        $view->headScript()->appendFile($view->assetUrl('js/load-assets.js', 'ExtraBlocks'));
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
        $perPage           = htmlspecialchars($data['per_page'] ?? '12');
        $sortBy            = $data['sort_by'] ?? 'title';
        $itemWidth         = htmlspecialchars($data['item_width'] ?? '');
        $itemHeight        = htmlspecialchars($data['item_height'] ?? '');
        $borderColor       = htmlspecialchars($data['border_color'] ?? '#ff0000');
        $borderTransparent = !empty($data['border_transparent']) ? ' checked' : '';
        $borderRadius      = htmlspecialchars($data['border_radius'] ?? '');

        $itemSetSidebarUrl = $view->url('admin/default', [
            'controller' => 'item-set',
            'action'     => 'sidebar-select',
        ]);

        $html = '<div class="item-set-grid-form">';

        // Item set picker
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>' . $view->translate('Item Set') . '</label></div>';
        $html .= '<div class="inputs">';
        $html .= '<div class="siteblocks-asset-picker">';

        $html .= '<input type="hidden" class="siteblocks-asset-id" name="o:block[__blockIndex__][o:data][item_set_id]" value="' . htmlspecialchars($itemSetId) . '">';
        $html .= '<input type="hidden" class="siteblocks-asset-name" name="o:block[__blockIndex__][o:data][item_set_name]" value="' . $itemSetName . '">';

        $previewStyle = $itemSetId ? '' : ' style="display:none"';
        $html .= '<div class="siteblocks-asset-preview"' . $previewStyle . '>';
        $html .= '<span class="siteblocks-preview-name">' . $itemSetName . '</span>';
        $html .= '</div>';

        $clearStyle = $itemSetId ? '' : ' style="display:none"';
        $html .= '<a href="#" class="siteblocks-asset-clear button alert"' . $clearStyle . '>' . $view->translate('Clear') . '</a> ';
        $html .= '<a href="#" class="siteblocks-item-set-select button" data-sidebar-url="' . htmlspecialchars($itemSetSidebarUrl) . '">' . $view->translate('Select item set') . '</a>';

        $html .= '</div>'; // .siteblocks-asset-picker
        $html .= '</div></div>';

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

        // JS for item set sidebar
        $html .= '<script>
(function($) {
    $(document).ready(function() {
        var activeItemSetPicker = null;

        if (!$("#siteblocks-item-set-sidebar").length) {
            $("#content").append("<div id=\"siteblocks-item-set-sidebar\" class=\"sidebar\"><div class=\"sidebar-content\"></div></div>");
        }
        var itemSetSidebar = $("#siteblocks-item-set-sidebar");

        $(document).on("click", ".siteblocks-item-set-select", function(e) {
            e.preventDefault();
            activeItemSetPicker = $(this).closest(".siteblocks-asset-picker");
            var sidebarUrl = $(this).data("sidebar-url");
            Omeka.openSidebar(itemSetSidebar);
            Omeka.populateSidebarContent(itemSetSidebar, sidebarUrl, function() {
                itemSetSidebar.trigger("o:sidebar-content-loaded");
            });
        });

        $(document).on("click", "#siteblocks-item-set-sidebar .select-resource", function(e) {
            e.preventDefault();
            if (!activeItemSetPicker) return;

            var row  = $(this).closest(".resource");
            var id   = row.find(".select-resource-checkbox").val();
            var name = row.find(".resource-name").text().trim();

            activeItemSetPicker.find(".siteblocks-asset-id").val(id);
            activeItemSetPicker.find(".siteblocks-asset-name").val(name);
            activeItemSetPicker.find(".siteblocks-preview-name").text(name);
            activeItemSetPicker.find(".siteblocks-asset-preview").show();
            activeItemSetPicker.find(".siteblocks-asset-clear").show();

            Omeka.closeSidebar(itemSetSidebar);
            activeItemSetPicker = null;
        });

        $(document).on("click", "#siteblocks-item-set-sidebar .sidebar-close", function(e) {
            e.preventDefault();
            Omeka.closeSidebar(itemSetSidebar);
        });
    });
})(jQuery);
</script>';

        return $html;
    }

public function render(PhpRenderer $view, SitePageBlockRepresentation $block)
{
    $data          = $block->data();
    $itemSetId     = $data['item_set_id'] ?? null;
    $perPage       = (int) ($data['per_page'] ?? 12);
    $defaultSortBy = $data['sort_by'] ?? 'title';
    
    // 1. Get current state from URL
    $page             = (int) $view->params()->fromQuery('grid_page', 1);
    $currentSortBy    = $view->params()->fromQuery('sort_by', $defaultSortBy);
    $currentSortOrder = $view->params()->fromQuery('sort_order', 'asc');

    if (!$itemSetId) {
        return '<p>' . $view->translate('No item set selected.') . '</p>';
    }

    // 2. Prepare API Sort Params
    // Omeka S expects 'property' for DC terms, or internal keys like 'title' or 'created'
    $apiSortBy = $currentSortBy;
    if (strpos($currentSortBy, ':') !== false) {
        // If it's dcterms:creator, the API wants the ID or the term string
        $apiSortBy = $currentSortBy; 
    }

    try {
        $response = $view->api()->search('items', [
            'item_set_id' => $itemSetId,
            'page'        => $page,
            'per_page'    => $perPage,
            'sort_by'     => $apiSortBy,
            'sort_order'  => $currentSortOrder,
        ]);

        $items      = $response->getContent();
        $totalCount = $response->getTotalResults();
        $totalPages = (int) ceil($totalCount / $perPage);

    } catch (\Exception $e) {
        return '<p>' . $view->translate('Could not load items.') . '</p>';
    }

    $itemWidth    = $data['item_width'] ?? '330px';
    $itemHeight   = $data['item_height'] ?? '400px';
    $borderRadius = $data['border_radius'] ?? '25px';
    $borderColor  = !empty($data['border_transparent']) ? 'transparent' : ($data['border_color'] ?? '#ff0000');

    return $view->partial('common/block-layout/item-set-grid', [
        'items'        => $items,
        'page'         => $page,
        'totalPages'   => $totalPages,
        'perPage'      => $perPage,
        'sortBy'       => $currentSortBy,
        'sortOrder'    => $currentSortOrder,
        'itemWidth'    => $itemWidth,
        'itemHeight'   => $itemHeight,
        'borderRadius' => $borderRadius,
        'borderColor'  => $borderColor,
        'totalCount'   => $totalCount,
    ]);
}

    public function prepareRender(PhpRenderer $view)
    {
        $view->headStyle()->appendStyle(file_get_contents(__DIR__ . '/../../../asset/css/item-set-grid.css'));
    }
}