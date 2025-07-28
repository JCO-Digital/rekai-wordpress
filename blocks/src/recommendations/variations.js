import { __, sprintf } from "@wordpress/i18n";
import icons from "./icons";

const variations = [
  {
    name: "recommendations",
    title: "Rek.ai Recommendations",
    icon: icons.link,
    description: __("Recommendations block using Rek.ai.", "rekai"),
    attributes: { blockType: "recommendations" },
    isActive: ["blockType"],
    isDefault: true,
    scope: ["inserter", "transform"],
  },
  {
    name: "qna",
    title: "Rek.ai Questions and Answers",
    icon: icons.question,
    description: __("Rek.ai Questions and answers block.", "rekai"),
    attributes: { blockType: "qna" },
    isActive: ["blockType"],
    scope: ["inserter", "transform"],
  },
];

export default variations;
