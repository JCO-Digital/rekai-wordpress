/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { InspectorControls, useBlockProps } from "@wordpress/block-editor";

import {
  FormTokenField,
  PanelBody,
  RangeControl,
  TextControl,
  ToggleControl,
} from "@wordpress/components";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

import logo from "../../../assets/img/logo-rekai-blue.svg";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {JSX.Element} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
  const { nrofhits, headerText, tags, useRoot } = attributes;

  return (
    <div {...useBlockProps()}>
      <img src={logo} alt={"Rek.ai Logo"} />
      <p>Q&A Block</p>
      <InspectorControls>
        <PanelBody title={__("Settings", "rekai-wordpress")} initialOpen={true}>
          <TextControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            help={__(
              "If you want to add header text above the questions",
              "rekai-wordpress",
            )}
            label={__("Header Text", "rekai-wordpress")}
            value={headerText}
            onChange={(value) => setAttributes({ headerText: value })}
          />
          <RangeControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            label={__("Number of Questions to show", "rekai-wordpress")}
            value={nrofhits}
            onChange={(value) => setAttributes({ nrofhits: value })}
            min={1}
            max={5}
          />
          <ToggleControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            label={__("Use Root path", "rekai-wordpress")}
            help={__(
              "Enabling this will show only questions that are under the path where this block is added",
              "rekai-wordpress",
            )}
            checked={useRoot}
            onChange={(value) => {
              setAttributes({ useRoot: value });
            }}
          />
          <FormTokenField
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            label="Tags"
            onChange={(values) => {
              setAttributes({ tags: values });
            }}
            suggestions={[]}
            value={tags}
          />
        </PanelBody>
      </InspectorControls>
    </div>
  );
}
