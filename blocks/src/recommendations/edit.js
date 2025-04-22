import { __ } from "@wordpress/i18n";
import { useState, useEffect } from "@wordpress/element";
import { useEntityRecords } from "@wordpress/core-data";
import {
  FormTokenField,
  PanelBody,
  TextControl,
  ToggleControl,
  RadioControl,
  Spinner,
  SelectControl,
} from "@wordpress/components";
import {
  InspectorAdvancedControls,
  InspectorControls,
  RichText,
  useBlockProps,
} from "@wordpress/block-editor";
import logo from "../../../assets/img/logo-rekai-blue.svg";
import "./editor.scss";
import usePosts from "./usePosts";

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
    headerText,
    showHeader,
    nrOfHits,
    tags,
    pathOption,
    limitations,
    rootPathLevel,
    limitDepth,
    subTree,
    extraAttributes,
  } = attributes;
  const separator = "##!!##";
  const isRecommendations = blockType === "recommendations";
  const isQna = !isRecommendations;
  const [tokenValue, setTokenValue] = useState([]);
  const postList = usePosts(subTree, setTokenValue, separator);

  return (
    <div {...useBlockProps({ className: blockType })}>
      <div className="logoHeader">
        <img src={logo} alt={"Rek.ai Logo"} />
        <h4>
          {(blockType === "recommendations" &&
            __("Recommendations", "rekai-wordpress")) ||
            __("Questions & Answers", "rekai-wordpress")}
        </h4>
      </div>
      {showHeader && (
        <RichText
          identifier="headerText"
          tagName={"h2"}
          value={headerText}
          onChange={(newValue) => {
            setAttributes({ headerText: newValue });
          }}
          placeholder={__("Heading Text", "rekai-wordpress")}
        />
      )}

      {(isRecommendations && renderRecommendations(attributes)) ||
        renderQna(attributes)}

      <InspectorControls>
        <PanelBody title={__("Display", "rekai-wordpress")}>
          <ToggleControl
            label={__("Show Header", "rekai-wordpress")}
            checked={showHeader}
            onChange={(newValue) => {
              setAttributes({ showHeader: newValue });
            }}
            __next40pxDefaultSize
            __nextHasNoMarginBottom
          />
          <TextControl
            label={__("Number of Hits", "rekai-wordpress")}
            type="number"
            onChange={(newValue) => {
              setAttributes({ nrOfHits: limitValue(newValue, 1, 100) });
            }}
            value={nrOfHits}
            min={1}
            max={100}
            __next40pxDefaultSize
            __nextHasNoMarginBottom
          />
          {isRecommendations && renderStyleHandler(attributes, setAttributes)}
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
            __next40pxDefaultSize
            __nextHasNoMarginBottom
          />
          {isQna && (
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
          )}
          <RadioControl
            label={__("Recommendation source:", "rekai-wordpress")}
            selected={pathOption}
            options={[
              {
                value: "all",
                label: __("Entire website", "rekai-wordpress"),
              },
              {
                value: "rootPath",
                label: __(
                  "Only subpages to the currently visited page",
                  "rekai-wordpress",
                ),
              },
              {
                value: "subTree",
                label: __(
                  "Only subpages to a specific starting point",
                  "rekai-wordpress",
                ),
              },
              {
                value: "rootPathLevel",
                label: __(
                  "Only subpages in the current website segment from level",
                  "rekai-wordpress",
                ),
              },
            ]}
            onChange={(value) => setAttributes({ pathOption: value })}
          />
          {pathOption === "rootPathLevel" && (
            <TextControl
              type="number"
              value={parseInt(rootPathLevel)}
              label={__("Level", "rekai-wordpress")}
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
              __nextHasNoMarginBottom
              label={__("Starting point", "rekai-wordpress")}
              placeholder={__("Search for Page", "jcore")}
              suggestions={postList}
              displayTransform={(token) => {
                const field = token.split(separator);
                return field[0] ?? "";
              }}
              value={tokenValue}
              onChange={(token) => {
                setTokenValue(token);
                let value = token.map((t) => {
                  const field = t.split(separator);
                  if (field.length === 1) {
                    return field[0];
                  } else if (field.length === 2) {
                    return field[1];
                  }
                  return undefined;
                });
                setAttributes({ subTree: value });
              }}
            />
          )}
          <RadioControl
            label={__("Limitations:", "rekai-wordpress")}
            selected={limitations}
            options={[
              {
                value: "none",
                label: __("None", "rekai-wordpress"),
              },
              {
                value: "subPages",
                label: __(
                  "Exclude subpages from starting point",
                  "rekai-wordpress",
                ),
              },
              {
                value: "childNodes",
                label: __("Exclude pages on the next level", "rekai-wordpress"),
              },
              {
                value: "onPageLinks",
                label: __(
                  "Exclude links already on the page",
                  "rekai-wordpress",
                ),
              },
            ]}
            onChange={(value) => setAttributes({ limitations: value })}
          />
        </PanelBody>
      </InspectorControls>
      <InspectorAdvancedControls>
        <TextControl
          __next40pxDefaultSize
          __nextHasNoMarginBottom
          value={extraAttributes}
          onChange={(value) => {
            setAttributes({ extraAttributes: value });
          }}
          label={__("Extra Rek.ai attributes", "rekai-wordpress")}
          help={__("Add extra Rek.ai attributes here.", "rekai-wordpress")}
        />
      </InspectorAdvancedControls>
    </div>
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
        label={__("Render Style", "rekai-wordpress")}
        value={renderStyle}
        options={[
          { label: __("Pills", "rekai-wordpress"), value: "pills" },
          { label: __("List", "rekai-wordpress"), value: "list" },
          {
            label: __("Advanced", "rekai-wordpress"),
            value: "advanced",
          },
        ]}
        onChange={(newValue) => setAttributes({ renderStyle: newValue })}
        __next40pxDefaultSize
        __nextHasNoMarginBottom
      />
      {renderStyle === "list" && (
        <TextControl
          label={__("Number of Columns", "rekai-wordpress")}
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
          label={__("Number of Columns", "rekai-wordpress")}
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
          label={__("Show Image", "rekai-wordpress")}
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
          label={__("Show Ingress", "rekai-wordpress")}
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
          label={__("Ingress Max Length", "rekai-wordpress")}
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
