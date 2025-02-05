import { __ } from "@wordpress/i18n";
import { useState } from "@wordpress/element";
import { PanelBody, TextControl, ToggleControl } from "@wordpress/components";
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
          <TextControl
            label={__("Number of Recommendations", "rekai-wordpress")}
            type="number"
            onChange={(newValue) => {
              setAttributes({ nrofhits: newValue });
            }}
            value={attributes.nrofhits}
          />
          <ToggleControl
            label={__("Add content", "rekai-wordpress")}
            help={
              attributes.addcontent
                ? __("Adds text content to data.", "rekai-wordpress")
                : __("Only use metadata.", "rekai-wordpress")
            }
            checked={attributes.addcontent}
            onChange={(newValue) => {
              setAttributes({ addcontent: newValue });
            }}
          />
        </PanelBody>
        <PanelBody title={__("Filter", "rekai-wordpress")}>
          <ToggleControl
            label={__("Show only current language", "rekai-wordpress")}
            help={
              attributes.currentLanguage
                ? __(
                    "Shows only content in current language.",
                    "rekai-wordpress",
                  )
                : __("Shows content in all languages.", "rekai-wordpress")
            }
            checked={attributes.currentLanguage}
            onChange={(newValue) => {
              setAttributes({ currentLanguage: newValue });
            }}
          />
          <TextControl
            label={__("Subtree", "rekai-wordpress")}
            type="text"
            onChange={(newValue) => {
              setAttributes({ subtree: newValue });
            }}
            value={attributes.subtree}
          />
        </PanelBody>
      </InspectorControls>

      {__("Rek.ai Recommendations", "rekai-wordpress")}
    </div>
  );
}
