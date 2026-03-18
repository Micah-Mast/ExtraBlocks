<?php
namespace ExtraBlocks\Site\BlockLayout;

use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SiteRepresentation;
use Laminas\View\Renderer\PhpRenderer;

class ColorOrImageHeader extends AbstractBlockLayout
{
    public function getLabel()
    {
        return 'ColorOrImageHeader';
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

        $text             = htmlspecialchars($data['text'] ?? '');
        $height           = htmlspecialchars($data['height'] ?? '20vh');
        $imageSize        = htmlspecialchars($data['image_size'] ?? 'cover');
        $verticalPosition = htmlspecialchars($data['vertical_position'] ?? '50%');
        $overlayColor     = htmlspecialchars($data['overlay_color'] ?? '#000000');
        $overlayOpacity   = htmlspecialchars($data['overlay_opacity'] ?? '0');
        $overlayHeight    = htmlspecialchars($data['overlay_height'] ?? $height);
        $overlapTop       = htmlspecialchars($data['overlap_top'] ?? '0px');
        $overlapBottom    = htmlspecialchars($data['overlap_bottom'] ?? '0px');
        $zIndex           = htmlspecialchars($data['z_index'] ?? '0');
        $assetId          = $data['asset_id'] ?? '';
        $assetUrl         = htmlspecialchars($data['asset_url'] ?? '');
        $assetName        = htmlspecialchars($data['asset_name'] ?? '');

        $sidebarUrl = $view->url('admin/default', [
            'controller' => 'asset',
            'action'     => 'sidebar-select',
        ]);

        $html = '<div class="header-block-form">';

        // Heading
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Text</label></div>';
        $html .= '<div class="inputs"><textarea name="o:block[__blockIndex__][o:data][text]">' . $text . '</textarea></div>';        $html .= '</div>';
        // Text color
        $textColor = htmlspecialchars($data['text_color'] ?? '#ffffff');
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Text color</label></div>';
        $html .= '<div class="inputs"><input type="color" name="o:block[__blockIndex__][o:data][text_color]" value="' . $textColor . '"></div>';
        $html .= '</div>';

        // Height
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Height (CSS value e.g. 40vh, 300px)</label></div>';
        $html .= '<div class="inputs"><input type="text" name="o:block[__blockIndex__][o:data][height]" value="' . $height . '"></div>';
        $html .= '</div>';

        // Image size
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Image size (e.g. cover, contain, 150%, 300px)</label></div>';
        $html .= '<div class="inputs"><input type="text" name="o:block[__blockIndex__][o:data][image_size]" value="' . $imageSize . '"></div>';
        $html .= '</div>';

        // Vertical position
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Image vertical position (e.g. 0% = top, 50% = center, 100% = bottom)</label></div>';
        $html .= '<div class="inputs"><input type="text" name="o:block[__blockIndex__][o:data][vertical_position]" value="' . $verticalPosition . '"></div>';
        $html .= '</div>';

        // Overlay color
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Overlay color</label></div>';
        $html .= '<div class="inputs"><input type="color" name="o:block[__blockIndex__][o:data][overlay_color]" value="' . $overlayColor . '"></div>';
        $html .= '</div>';

        // Overlay opacity
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Overlay opacity (0 = none, 1 = full)</label></div>';
        $html .= '<div class="inputs"><input type="range" min="0" max="1" step="0.05" name="o:block[__blockIndex__][o:data][overlay_opacity]" value="' . $overlayOpacity . '" style="width:200px;"></div>';
        $html .= '</div>';

        // Height
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Overlay Height (CSS value e.g. 40vh, 300px, 50%)</label></div>';
        $html .= '<div class="inputs"><input type="text" name="o:block[__blockIndex__][o:data][overlay_height]" value="' . $overlayHeight . '"></div>';
        $html .= '</div>';

        // Previous element Overlap
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Previous element overlap (CSS value e.g. 50px, 0px to disable)</label></div>';
        $html .= '<div class="inputs"><input type="text" name="o:block[__blockIndex__][o:data][overlap_top]" value="' . $overlapTop . '"></div>';
        $html .= '</div>';

        // Next element Overlap
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Next element overlap (CSS value e.g. 50px, 0px to disable)</label></div>';
        $html .= '<div class="inputs"><input type="text" name="o:block[__blockIndex__][o:data][overlap_bottom]" value="' . $overlapBottom . '"></div>';
        $html .= '</div>';

        // Z-index
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Z-index (integer, default 0)</label></div>';
        $html .= '<div class="inputs"><input type="number" name="o:block[__blockIndex__][o:data][z_index]" value="' . $zIndex . '"></div>';
        $html .= '</div>';

        // Asset picker
        $html .= '<div class="field">';
        $html .= '<div class="field-meta"><label>Banner Image</label></div>';
        $html .= '<div class="inputs">';
        $html .= '<div class="siteblocks-asset-picker">';

        $html .= '<input type="hidden" class="siteblocks-asset-id" name="o:block[__blockIndex__][o:data][asset_id]" value="' . htmlspecialchars($assetId) . '">';
        $html .= '<input type="hidden" class="siteblocks-asset-url" name="o:block[__blockIndex__][o:data][asset_url]" value="' . $assetUrl . '">';
        $html .= '<input type="hidden" class="siteblocks-asset-name" name="o:block[__blockIndex__][o:data][asset_name]" value="' . $assetName . '">';

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

        $html .= '</div>'; // .header-block-form

        return $html;
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block)
    {
        $data = $block->data();

        $assetId          = $data['asset_id'] ?? '';
        $assetUrl         = $data['asset_url'] ?? '';
        $text             = $data['text'] ?? '';
        $height           = $data['height'] ?? '20vh';
        $overlapBottom    = $data['overlap_bottom'] ?? '0px';
        $overlapTop       = $data['overlap_top'] ?? '0px';
        $imageSize        = $data['image_size'] ?? 'cover';
        $verticalPosition = $data['vertical_position'] ?? '50%';
        $overlayColor     = $data['overlay_color'] ?? '#000000';
        $overlayOpacity   = $data['overlay_opacity'] ?? '0';
        $overlayHeight    = $data['overlay_height'] ?? $height;

        if ($assetId && !$assetUrl) {
            try {
                $asset = $view->api()->read('assets', $assetId)->getContent();
                $assetUrl = $asset->assetUrl();
            } catch (\Exception $e) {
                $assetUrl = '';
            }
        }

        return $view->partial('common/block-layout/color-or-image-header', [
            'assetUrl'         => $assetUrl,
            'text'             => $text,
            'height'           => $height,
            'overlapBottom'    => $overlapBottom,
            'overlapTop'       => $overlapTop,
            'imageSize'        => $imageSize,
            'verticalPosition' => $verticalPosition,
            'overlayColor'     => $overlayColor,
            'overlayOpacity'   => $overlayOpacity,
            'textColor'        => $data['text_color'] ?? '#ffffff',
            'overlayHeight'    => $overlayHeight,
            'zIndex'           => $data['z_index'] ?? '0',
        ]);
    }
        public function prepareRender(PhpRenderer $view)
    {
        $view->headStyle()->appendStyle(file_get_contents(__DIR__ . '/../../../asset/css/color-or-image-header.css'));
    }
}