<?php
namespace ExtraBlocks\Site\BlockLayout;

use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SiteRepresentation;
use Laminas\View\Renderer\PhpRenderer;

class NavigationButtons extends AbstractBlockLayout
{
    public function getLabel()
    {
        return 'Navigation Buttons';
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
        $data        = $block ? $block->data() : [];
        $buttonCount = $data['button_count'] ?? 2;
        $buttons     = $data['buttons'] ?? [];

        $sidebarUrl = $view->url('admin/default', [
            'controller' => 'asset',
            'action'     => 'sidebar-select',
        ]);

        $html = '<div class="nav-buttons-form">';

        // Text color
        $textColor = htmlspecialchars($data['text_color'] ?? '#111111');
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Text color</label></div>';
        $html .= '<div class="inputs"><input type="color" name="o:block[__blockIndex__][o:data][text_color]" value="' . $textColor . '"></div>';
        $html .= '</div>';

        // Background color
        $bgColor       = htmlspecialchars($data['bg_color'] ?? '#ffffff');
        $bgTransparent = !empty($data['bg_transparent']) ? ' checked' : '';
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Background color</label></div>';
        $html .= '<div class="inputs" style="display:flex;align-items:center;gap:10px;">';
        $html .= '<input type="color" name="o:block[__blockIndex__][o:data][bg_color]" value="' . $bgColor . '"' . (!empty($data['bg_transparent']) ? ' disabled' : '') . '>';
        $html .= '<label style="display:flex;align-items:center;gap:5px;">';
        $html .= '<input type="checkbox" class="siteblocks-transparent-check" name="o:block[__blockIndex__][o:data][bg_transparent]" value="1"' . $bgTransparent . '> Transparent';
        $html .= '</label>';
        $html .= '</div></div>';

        // Border color
        $borderColor       = htmlspecialchars($data['border_color'] ?? '#111111');
        $borderTransparent = !empty($data['border_transparent']) ? ' checked' : '';
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Border color (shows on hover)</label></div>';
        $html .= '<div class="inputs" style="display:flex;align-items:center;gap:10px;">';
        $html .= '<input type="color" name="o:block[__blockIndex__][o:data][border_color]" value="' . $borderColor . '"' . (!empty($data['border_transparent']) ? ' disabled' : '') . '>';
        $html .= '<label style="display:flex;align-items:center;gap:5px;">';
        $html .= '<input type="checkbox" class="siteblocks-transparent-check" name="o:block[__blockIndex__][o:data][border_transparent]" value="1"' . $borderTransparent . '> Transparent';
        $html .= '</label>';
        $html .= '</div></div>';

        // Button count selector
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Number of buttons</label></div>';
        $html .= '<div class="inputs"><select name="o:block[__blockIndex__][o:data][button_count]" class="nav-btn-count">';
        foreach ([2, 3, 4] as $n) {
            $selected = $buttonCount == $n ? ' selected' : '';
            $html .= "<option value=\"{$n}\"{$selected}>{$n}</option>";
        }
        $html .= '</select></div></div>';

        // Individual button fields
        for ($i = 0; $i < 4; $i++) {
            $label     = htmlspecialchars($buttons[$i]['label'] ?? '');
            $url       = htmlspecialchars($buttons[$i]['url'] ?? '');
            $assetId   = $buttons[$i]['asset_id'] ?? '';
            $assetUrl  = htmlspecialchars($buttons[$i]['asset_url'] ?? '');
            $assetName = htmlspecialchars($buttons[$i]['asset_name'] ?? '');
            $display   = $i < $buttonCount ? '' : ' style="display:none"';

            $html .= "<div class=\"nav-btn-group\" data-index=\"{$i}\"{$display}>";
            $html .= "<h4>Button " . ($i + 1) . "</h4>";

            // Label
            $html .= '<div class="field">';
            $html .= '<div class="field-meta"><label>Label</label></div>';
            $html .= '<div class="inputs"><input type="text" name="o:block[__blockIndex__][o:data][buttons][' . $i . '][label]" value="' . $label . '"></div>';
            $html .= '</div>';

            // URL
            $html .= '<div class="field">';
            $html .= '<div class="field-meta"><label>URL Slug</label></div>';
            $html .= '<div class="inputs"><input type="text" name="o:block[__blockIndex__][o:data][buttons][' . $i . '][url]" value="' . $url . '"></div>';
            $html .= '</div>';

            // Asset picker
            $html .= '<div class="field">';
            $html .= '<div class="field-meta"><label>Icon / Image</label></div>';
            $html .= '<div class="inputs">';
            $html .= '<div class="siteblocks-asset-picker">';

            $html .= '<input type="hidden" class="siteblocks-asset-id" name="o:block[__blockIndex__][o:data][buttons][' . $i . '][asset_id]" value="' . htmlspecialchars($assetId) . '">';
            $html .= '<input type="hidden" class="siteblocks-asset-url" name="o:block[__blockIndex__][o:data][buttons][' . $i . '][asset_url]" value="' . $assetUrl . '">';
            $html .= '<input type="hidden" class="siteblocks-asset-name" name="o:block[__blockIndex__][o:data][buttons][' . $i . '][asset_name]" value="' . $assetName . '">';

            $previewStyle = $assetId ? '' : ' style="display:none"';
            $html .= '<div class="siteblocks-asset-preview"' . $previewStyle . '>';
            $html .= '<img class="siteblocks-preview-img" src="' . $assetUrl . '" style="max-height:60px;display:block;margin-bottom:5px;">';
            $html .= '<span class="siteblocks-preview-name">' . $assetName . '</span>';
            $html .= '</div>';

            $clearStyle = $assetId ? '' : ' style="display:none"';
            $html .= '<a href="#" class="siteblocks-asset-clear button alert"' . $clearStyle . '>Clear</a> ';
            $html .= '<a href="#" class="siteblocks-asset-select button" data-sidebar-url="' . htmlspecialchars($sidebarUrl) . '">Select image</a>';

            $html .= '</div>'; // .siteblocks-asset-picker
            $html .= '</div></div>';

            $html .= '</div>'; // .nav-btn-group
        }

        $html .= '</div>'; // .nav-buttons-form

        return $html;
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block)
    {
        $data        = $block->data();
        $buttonCount = (int) ($data['button_count'] ?? 2);
        $buttons     = array_slice($data['buttons'] ?? [], 0, $buttonCount);

        foreach ($buttons as &$button) {
            if (!empty($button['asset_id']) && empty($button['asset_url'])) {
                try {
                    $asset = $view->api()->read('assets', $button['asset_id'])->getContent();
                    $button['asset_url'] = $asset->assetUrl();
                } catch (\Exception $e) {
                    // Asset not found, skip
                }
            }
        }

        $bgColor     = !empty($data['bg_transparent'])    ? 'transparent' : ($data['bg_color'] ?? '#ffffff');
        $borderColor = !empty($data['border_transparent']) ? 'transparent' : ($data['border_color'] ?? '#111111');

        return $view->partial('common/block-layout/navigation-buttons', [
            'buttons'     => $buttons,
            'textColor'   => $data['text_color'] ?? '#111111',
            'bgColor'     => $bgColor,
            'borderColor' => $borderColor,
        ]);
    }

    public function prepareRender(PhpRenderer $view)
    {
        $view->headStyle()->appendStyle(file_get_contents(__DIR__ . '/../../../asset/css/nav-buttons.css'));
    }
}