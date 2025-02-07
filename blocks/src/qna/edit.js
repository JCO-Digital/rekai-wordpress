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
  __experimentalSpacer as Spacer,
  __experimentalVStack as VStack,
  __experimentalNumberControl as NumberControl,
  RadioControl,
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
  const {
    nrofhits,
    headerText,
    tags,
    pathOption,
    extraAttributes,
    useCurrentLanguage,
    depth,
    limit,
    limitDepth,
  } = attributes;

  return (
    <div {...useBlockProps()}>
      <div className="rekai-block-heading">
        <img src={logo} alt={"Rek.ai Logo"} />
        <h4>Q&A Block</h4>
      </div>
      <div className="rekai-block-preview">
        {Array.from(Array(nrofhits).keys()).map((i) => (
          <div key={i} className="rekai-block-preview-item">
            <div className="rekai-block-preview-blob"></div>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              className="lucide lucide-plus"
            >
              <path d="M5 12h14" />
              <path d="M12 5v14" />
            </svg>
          </div>
        ))}
      </div>

      <InspectorControls>
        <PanelBody title={__("Filters", "rekai-wordpress")} initialOpen={true}>
          <VStack gap={4}>
            <ToggleControl
              __next40pxDefaultSize
              __nextHasNoMarginBottom
              label={__("Show only current language", "rekai-wordpress")}
              help={
                attributes.useCurrentLanguage
                  ? __(
                      "Shows only content in current language.",
                      "rekai-wordpress",
                    )
                  : __("Shows content in all languages.", "rekai-wordpress")
              }
              checked={useCurrentLanguage}
              onChange={(value) => {
                setAttributes({ useCurrentLanguage: value });
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
            <RadioControl
              label={__("Show content from starting point:", "rekai-wordpress")}
              selected={pathOption}
              options={[
                {
                  value: "all",
                  label: __("Whole website", "rekai-wordpress"),
                },
                {
                  value: "rootPath",
                  label: __("Starting from current page", "rekai-wordpress"),
                },
                {
                  value: "maxDepth",
                  label: __(
                    "Subpages until specified depth",
                    "rekai-wordpress",
                  ),
                },
                {
                  value: "rootPathLevel",
                  label: __(
                    "Subpages of current page from specified depth",
                    "rekai-wordpress",
                  ),
                },
              ]}
              onChange={(value) => setAttributes({ pathOption: value })}
            />
            {["maxDepth", "rootPathLevel"].includes(pathOption) && (
              <NumberControl
                value={parseInt(depth)}
                label={
                  pathOption === "maxDepth"
                    ? __("Max depth", "rekai-wordpress")
                    : __(
                        "Path level from current path to exclude",
                        "rekai-wordpress",
                      )
                }
                min={0}
                onChange={(value) => setAttributes({ depth: parseInt(value) })}
              />
            )}
            <RadioControl
              label={__("Exclude content from subpages?", "rekai-wordpress")}
              selected={limit}
              options={[
                {
                  value: "none",
                  label: __("None", "rekai-wordpress"),
                },
                {
                  value: "subPages",
                  label: __("Subpages of current page", "rekai-wordpress"),
                },
                {
                  value: "minDepth",
                  label: __("Exclude subpages until depth", "rekai-wordpress"),
                },
              ]}
              onChange={(value) => setAttributes({ limit: value })}
            />
            {limit === "minDepth" && (
              <NumberControl
                value={parseInt(limitDepth)}
                label={__("Exclude subpages until depth")}
                min={0}
                onChange={(value) =>
                  setAttributes({ limitDepth: parseInt(value) })
                }
              />
            )}
          </VStack>
        </PanelBody>
        <PanelBody title={__("Display", "rekai-wordpress")}>
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
        </PanelBody>
        <PanelBody title={__("Extra attributes", "rekai-wordpress")}>
          <TextControl
            __next40pxDefaultSize
            __nextHasNoMarginBottom
            value={extraAttributes}
            onChange={(value) => {
              setAttributes({ extraAttributes: value });
            }}
            label={__("Extra attributes", "rekai-wordpress")}
          />
        </PanelBody>
      </InspectorControls>
    </div>
  );
}
