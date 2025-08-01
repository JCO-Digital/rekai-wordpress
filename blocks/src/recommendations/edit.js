import { __ } from "@wordpress/i18n";
import { useState } from "@wordpress/element";
import {
  FormTokenField,
  PanelBody,
  TextControl,
  ToggleControl,
  RadioControl,
  SelectControl,
} from "@wordpress/components";
import {
  InspectorAdvancedControls,
  InspectorControls,
  BlockControls,
  RichText,
  useBlockProps,
} from "@wordpress/block-editor";
import logo from "../../../assets/img/logo-rekai-blue.svg";
import "./editor.scss";
import usePosts from "./usePosts";
import { tokenFieldHandler, displayTransform } from "./tokenFieldHandler";
import HeadingLevelDropdown from "./headingDropdown";

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
    blockType,
    headerHeadingLevel,
    headerText,
    showHeader,
    nrOfHits,
    tags,
    pathOption,
    limitations,
    rootPathLevel,
    subTree,
    showLangs,
    allowedLangs,
    excludeTree,
    extraAttributes,
  } = attributes;
  const isRecommendations = blockType === "recommendations";
  const isQna = !isRecommendations;
  const [subTreeTokenValue, setSubTreeTokenValue] = useState([]);
  const [excludeTreeTokenValue, setExcludeTreeTokenValue] = useState([]);
  const postList = usePosts(
    subTree,
    excludeTree,
    setSubTreeTokenValue,
    setExcludeTreeTokenValue,
  );

  return (
    <>
      {showHeader && (
        <BlockControls group="block">
          <HeadingLevelDropdown
            selectedLevel={headerHeadingLevel}
            onChange={(newLevel) =>
              setAttributes({ headerHeadingLevel: newLevel })
            }
          />
        </BlockControls>
      )}
      <div {...useBlockProps({ className: blockType })}>
        <div className="logoHeader">
          <img src={logo} alt={"Rek.ai Logo"} />
          <h4>
            {(blockType === "recommendations" &&
              __("Recommendations", "rekai")) ||
              __("Questions & Answers", "rekai")}
          </h4>
        </div>
        {showHeader && (
          <RichText
            identifier="headerText"
            tagName={"h" + headerHeadingLevel}
            value={headerText}
            onChange={(newValue) => {
              setAttributes({ headerText: newValue });
            }}
            placeholder={__("Heading Text", "rekai")}
          />
        )}

        {(isRecommendations && renderRecommendations(attributes)) ||
          renderQna(attributes)}

        <InspectorControls>
          <PanelBody title={__("Display", "rekai")}>
            <ToggleControl
              label={__("Show Header", "rekai")}
              checked={showHeader}
              onChange={(newValue) => {
                setAttributes({ showHeader: newValue });
              }}
              __next40pxDefaultSize
            />
            <TextControl
              label={__("Number of Hits", "rekai")}
              type="number"
              onChange={(newValue) => {
                setAttributes({ nrOfHits: limitValue(newValue, 1, 100) });
              }}
              value={nrOfHits}
              min={1}
              max={100}
              __next40pxDefaultSize
            />
            {isRecommendations && renderStyleHandler(attributes, setAttributes)}
          </PanelBody>
          <PanelBody title={__("Filter", "rekai")}>
            <ToggleControl
              label={__("Set Language", "rekai")}
              help={__("Enable to set which languages are shown.", "rekai")}
              checked={showLangs}
              onChange={(newValue) => {
                setAttributes({ showLangs: newValue });
              }}
              __next40pxDefaultSize
            />
            {showLangs && (
              <TextControl
                label={__("Allowed Languages", "rekai")}
                onChange={(newValue) => {
                  setAttributes({ allowedLangs: newValue });
                }}
                value={allowedLangs}
                help={__(
                  "If left empty, will use the language of the page.",
                  "rekai",
                )}
                __next40pxDefaultSize
              />
            )}

            {isQna && (
              <FormTokenField
                __next40pxDefaultSize
                label="Tags"
                onChange={(values) => {
                  setAttributes({ tags: values });
                }}
                suggestions={[]}
                value={tags}
              />
            )}
            <RadioControl
              label={__("Recommendation source:", "rekai")}
              selected={pathOption}
              options={[
                {
                  value: "all",
                  label: __("Entire website", "rekai"),
                },
                {
                  value: "rootPath",
                  label: __(
                    "Only subpages to the currently visited page",
                    "rekai",
                  ),
                },
                {
                  value: "subTree",
                  label: __(
                    "Only subpages to a specific starting point",
                    "rekai",
                  ),
                },
                {
                  value: "rootPathLevel",
                  label: __(
                    "Only subpages in the current website segment from level",
                    "rekai",
                  ),
                },
              ]}
              onChange={(value) => setAttributes({ pathOption: value })}
            />
            {pathOption === "rootPathLevel" && (
              <TextControl
                type="number"
                value={parseInt(rootPathLevel)}
                label={__("Level", "rekai")}
                min={0}
                onChange={(value) =>
                  setAttributes({ rootPathLevel: parseInt(value) })
                }
              />
            )}
            {pathOption === "subTree" && (
              <FormTokenField
                __experimentalExpandOnFocus
                __next40pxDefaultSize
                label={__("Starting point", "rekai")}
                placeholder={__("Search for Page", "jcore")}
                suggestions={postList}
                displayTransform={displayTransform}
                value={subTreeTokenValue}
                onChange={(token) => {
                  setSubTreeTokenValue(token);
                  setAttributes({ subTree: tokenFieldHandler(token) });
                }}
              />
            )}
            <RadioControl
              label={__("Limitations:", "rekai")}
              selected={limitations}
              options={[
                {
                  value: "none",
                  label: __("None", "rekai"),
                },
                {
                  value: "subPages",
                  label: __("Exclude subpages from starting point", "rekai"),
                },
                {
                  value: "childNodes",
                  label: __("Exclude pages on the next level", "rekai"),
                },
                {
                  value: "onPageLinks",
                  label: __("Exclude links already on the page", "rekai"),
                },
              ]}
              onChange={(value) => setAttributes({ limitations: value })}
            />
            {limitations === "subPages" && (
              <FormTokenField
                __experimentalExpandOnFocus
                __next40pxDefaultSize
                label={__("Starting point", "rekai")}
                placeholder={__("Search for Page", "jcore")}
                suggestions={postList}
                displayTransform={displayTransform}
                value={excludeTreeTokenValue}
                onChange={(token) => {
                  setExcludeTreeTokenValue(token);
                  setAttributes({ excludeTree: tokenFieldHandler(token) });
                }}
              />
            )}
          </PanelBody>
          <PanelBody title={__("Additional Settings", "rekai")}>
            <TextControl
              __next40pxDefaultSize
              value={extraAttributes}
              onChange={(value) => {
                setAttributes({ extraAttributes: value });
              }}
              label={__("Extra attributes", "rekai")}
              help={__("Add extra attributes here.", "rekai")}
            />
          </PanelBody>
        </InspectorControls>
      </div>
    </>
  );
}

function renderRecommendations(attributes) {
  const { nrOfHits, showImage, showIngress, renderStyle, listCols, cols } =
    attributes;

  const items = [];
  for (let i = 0; i < nrOfHits; i++) {
    items.push(
      <div key={i} className="item">
        {showImage && <div className="image"></div>}
        <div className="title"></div>
        {showIngress && <div className="row row1"></div>}
        {showIngress && <div className="row row2"></div>}
      </div>,
    );
  }

  return (
    <div
      className={
        "rekai-recommendations-preview cols" +
        (renderStyle === "list" ? listCols : cols) +
        " " +
        renderStyle
      }
    >
      {items}
    </div>
  );
}

function renderQna(attributes) {
  const { nrOfHits } = attributes;

  const items = [];
  for (let i = 0; i < nrOfHits; i++) {
    items.push(
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
      </div>,
    );
  }

  return <div className="rekai-qna-preview">{items}</div>;
}

function renderStyleHandler(attributes, setAttributes) {
  const {
    renderStyle,
    listCols,
    cols,
    showImage,
    showIngress,
    ingressMaxLength,
  } = attributes;

  return (
    <div>
      <SelectControl
        label={__("Render Style", "rekai")}
        value={renderStyle}
        options={[
          { label: __("Pills", "rekai"), value: "pills" },
          { label: __("List", "rekai"), value: "list" },
          {
            label: __("Advanced", "rekai"),
            value: "advanced",
          },
        ]}
        onChange={(newValue) => setAttributes({ renderStyle: newValue })}
        __next40pxDefaultSize
        __nextHasNoMarginBottom
      />
      {renderStyle === "list" && (
        <TextControl
          label={__("Number of Columns", "rekai")}
          type="number"
          onChange={(newValue) => {
            setAttributes({ listCols: limitValue(newValue, 1, 3) });
          }}
          value={listCols}
          min="1"
          max="3"
          __next40pxDefaultSize
          __nextHasNoMarginBottom
        />
      )}
      {renderStyle === "advanced" && (
        <TextControl
          label={__("Number of Columns", "rekai")}
          type="number"
          onChange={(newValue) => {
            setAttributes({ cols: limitValue(newValue, 1, 3) });
          }}
          value={cols}
          min="1"
          max="3"
          __next40pxDefaultSize
          __nextHasNoMarginBottom
        />
      )}
      {renderStyle === "advanced" && (
        <ToggleControl
          label={__("Show Image", "rekai")}
          checked={showImage}
          onChange={(newValue) => {
            setAttributes({ showImage: newValue });
          }}
          __next40pxDefaultSize
          __nextHasNoMarginBottom
        />
      )}
      {renderStyle === "advanced" && (
        <ToggleControl
          label={__("Show Ingress", "rekai")}
          checked={showIngress}
          onChange={(newValue) => {
            setAttributes({ showIngress: newValue });
          }}
          __next40pxDefaultSize
          __nextHasNoMarginBottom
        />
      )}
      {renderStyle === "advanced" && (
        <TextControl
          label={__("Ingress Max Length", "rekai")}
          type="number"
          value={ingressMaxLength}
          onChange={(newValue) => {
            setAttributes({ ingressMaxLength: newValue });
          }}
          __next40pxDefaultSize
          __nextHasNoMarginBottom
        />
      )}
    </div>
  );
}

function limitValue(value, min, max) {
  const intValue = parseInt(value);
  if (isNaN(intValue) || intValue < min) {
    return min;
  }
  if (intValue > max) {
    return max;
  }
  return intValue;
}
