import { __ } from "@wordpress/i18n";
import { useState } from "@wordpress/element";
import { PanelBody, TextControl } from "@wordpress/components";
import { InspectorControls, useBlockProps } from "@wordpress/block-editor";
import "./editor.scss";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {JSX.Element} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
  return (
    <div {...useBlockProps()}>
      <InspectorControls>
        <PanelBody title={__("Display", "rekai-wordpress")}>
          {__("Number of Recommendations", "rekai-wordpress")}
          <TextControl
            type="number"
            onChange={(val) => {
              setAttributes({ nrofhits: val });
            }}
            value={attributes.nrofhits}
          />
        </PanelBody>
        <PanelBody title={__("Filter", "rekai-wordpress")}></PanelBody>
      </InspectorControls>

      {__("Rek.ai Recommendations", "rekai-wordpress")}
    </div>
  );
}
