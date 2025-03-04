import { __, sprintf } from "@wordpress/i18n";

const variations = [
  {
    name: "recommendations",
    title: "Rek.ai Recommendations",
    icon: "editor-ul",
    description: __("Recommendations block using Rek.ai.", "rekai-wordpress"),
    attributes: { blockType: "recommendations" },
    isActive: ["blockType"],
    isDefault: true,
    scope: ["inserter", "transform"],
  },
  {
    name: "qna",
    title: "Rek.ai Questions and Answers",
    icon: "smiley",
    description: __("Rek.ai Questions and answers block.", "rekai-wordpress"),
    attributes: { blockType: "qna" },
    isActive: ["blockType"],
    scope: ["inserter", "transform"],
  },
];

export default variations;
