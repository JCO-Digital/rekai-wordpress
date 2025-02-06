import { __ } from "@wordpress/i18n";
import { useState } from "@wordpress/element";
import {
  PanelBody,
  TextControl,
  ToggleControl,
  SelectControl,
} from "@wordpress/components";
import {
  InspectorControls,
  RichText,
  useBlockProps,
} from "@wordpress/block-editor";
import logo from "../../../assets/img/logo-rekai-blue.svg";
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
  const { headerText, nrofhits, renderstyle } = attributes;
  const items = [];
  for (let i = 0; i < nrofhits; i++) {
    items.push(<div key={i} className="item"></div>);
  }
  return (
    <div {...useBlockProps()}>
      <RichText
        identifier="headerText"
        tagName={"h2"}
        value={headerText}
        onChange={(newValue) => {
          setAttributes({ headerText: newValue });
        }}
        placeholder={__("Heading Text", "rekai-wordpress")}
      />
      <div className={"items " + renderstyle}>{items}</div>

      <InspectorControls>
        <PanelBody title={__("Display", "rekai-wordpress")}>
          <TextControl
            label={__("Number of Recommendations", "rekai-wordpress")}
            type="number"
            onChange={(newValue) => {
              setAttributes({ nrofhits: newValue });
            }}
            value={nrofhits}
            __next40pxDefaultSize
            __nextHasNoMarginBottom
          />
          <SelectControl
            label={__("Render Style", "rekai-wordpress")}
            value={renderstyle}
            options={[
              { label: __("Pills", "rekai-wordpress"), value: "pills" },
              { label: __("List", "rekai-wordpress"), value: "list" },
              { label: __("Advanced", "rekai-wordpress"), value: "advanced" },
            ]}
            onChange={(newValue) => setAttributes({ renderstyle: newValue })}
            __next40pxDefaultSize
            __nextHasNoMarginBottom
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
            __next40pxDefaultSize
            __nextHasNoMarginBottom
          />
        </PanelBody>
        <PanelBody title={__("Filter", "rekai-wordpress")}>
          <ToggleControl
            label={__("Use Root path", "rekai-wordpress")}
            help={__(
              "Enabling this will show only questions that are under the path where this block is added",
              "rekai-wordpress",
            )}
            checked={attributes.userootpath}
            onChange={(value) => {
              setAttributes({ userootpath: value });
            }}
            __next40pxDefaultSize
            __nextHasNoMarginBottom
          />
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
            __next40pxDefaultSize
            __nextHasNoMarginBottom
          />
          <TextControl
            label={__("Subtree", "rekai-wordpress")}
            type="text"
            onChange={(newValue) => {
              setAttributes({ subtree: newValue });
            }}
            value={attributes.subtree}
            __next40pxDefaultSize
            __nextHasNoMarginBottom
          />
        </PanelBody>
      </InspectorControls>
    </div>
  );
}
