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
  __experimentalNumberControl as NumberControl,
} from "@wordpress/components";
import {
  InspectorAdvancedControls,
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
export default function Edit({ attributes, setAttributes, context }) {
  const {
    headerText,
    showHeader,
    nrOfHits,
    renderstyle,
    listcols,
    cols,
    showImage,
    showIngress,
    ingressMaxLength,
    pathOption,
    limit,
    depth,
    limitDepth,
    subtreeIds,
    extraAttributes,
  } = attributes;
  const separator = "##!!##";
  const [postList, setPostList] = useState([]);
  const [tokenValue, setTokenValue] = useState([]);
  const { hasResolved, records } = useEntityRecords(
    "postType",
    context.postType,
    {
      per_page: -1,
    },
  );

  useEffect(() => {
    if (records) {
      let tokenList = [];
      setPostList(
        records.map((record) => {
          const token =
            record.title.rendered + separator + record.id.toString();
          if (subtreeIds.includes(record.id.toString())) {
            tokenList.push(token);
          }
          return token;
        }),
      );
      setTokenValue(tokenList);
    }
  }, [records]);

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
    <div {...useBlockProps()}>
      <div className="logoHeader">
        <img src={logo} alt={"Rek.ai Logo"} />
        <h4>Recommendations</h4>
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
      <div
        className={
          "items cols" +
          (renderstyle === "list" ? listcols : cols) +
          " " +
          renderstyle
        }
      >
        {items}
      </div>

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
            label={__("Number of Recommendations", "rekai-wordpress")}
            type="number"
            onChange={(newValue) => {
              setAttributes({ nrOfHits: newValue });
            }}
            value={nrOfHits}
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
          {renderstyle === "list" && (
            <TextControl
              label={__("Number of Columns", "rekai-wordpress")}
              type="number"
              onChange={(newValue) => {
                setAttributes({ listcols: newValue });
              }}
              value={listcols}
              min="1"
              max="3"
              __next40pxDefaultSize
              __nextHasNoMarginBottom
            />
          )}
          {renderstyle === "advanced" && (
            <TextControl
              label={__("Number of Columns", "rekai-wordpress")}
              type="number"
              onChange={(newValue) => {
                setAttributes({ cols: newValue });
              }}
              value={cols}
              min="1"
              max="3"
              __next40pxDefaultSize
              __nextHasNoMarginBottom
            />
          )}
          {renderstyle === "advanced" && (
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
          {renderstyle === "advanced" && (
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
          {renderstyle === "advanced" && (
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
                label: __("Subpages until specified depth", "rekai-wordpress"),
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
          {["all"].includes(pathOption) &&
            ((hasResolved && (
              <FormTokenField
                __experimentalExpandOnFocus
                __next40pxDefaultSize
                __nextHasNoMarginBottom
                label={__("Subtree", "rekai-wordpress")}
                placeholder={__("Search for Page", "jcore")}
                suggestions={postList}
                displayTransform={(token) => {
                  const field = token.split(separator);
                  return field[0] ?? "";
                }}
                value={tokenValue}
                onChange={(token) => {
                  console.debug(token);
                  setTokenValue(token);
                  let value = token.map((t) => {
                    const field = t.split(separator);
                    if (field.length === 2) {
                      return field[1];
                    }
                    return undefined;
                  });
                  if (token.length) {
                  }
                  setAttributes({ subtreeIds: value });
                }}
              />
            )) || <Spinner />)}
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
