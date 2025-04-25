import { useState } from "@wordpress/element";
import { separator } from "./tokenFieldHandler";
let fetching = false;

export default function usePosts(
  subTree,
  excludeTree,
  setSubTreeTokenValue,
  setExcludeTreeTokenValue,
) {
  const url = "/?rest_route=/rekai/v1/posts";
  const [postList, setPostList] = useState([]);

  if (!fetching && postList.length === 0) {
    fetching = true;
    try {
      fetch(url).then((response) => {
        fetching = false;
        if (!response.ok) {
          throw new Error(`Response status: ${response.status}`);
        }
        const subTreeList = [];
        const excludeTreeList = [];
        response.json().then((body) => {
          let list = [...postList];
          body.forEach((post) => {
            const index = post.id ? post.id : post.link;
            const token = post.label + separator + index;
            list.push(token);

            // Add to SubTree.
            if (subTree.includes(String(index))) {
              subTreeList.push(token);
            }
            // Add to ExcludeTree.
            if (excludeTree.includes(String(index))) {
              excludeTreeList.push(token);
            }
          });
          setSubTreeTokenValue(subTreeList);
          setExcludeTreeTokenValue(excludeTreeList);
          setPostList(list);
        });
      });
    } catch (error) {
      fetching = false;
      console.error(error.message);
    }
  }

  return postList;
}
