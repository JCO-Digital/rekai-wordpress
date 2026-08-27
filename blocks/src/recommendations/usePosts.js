import apiFetch from "@wordpress/api-fetch";
import { useEffect, useRef, useState } from "@wordpress/element";
import { separator } from "./tokenFieldHandler";

export default function usePosts(
  subTree,
  excludeTree,
  setSubTreeTokenValue,
  setExcludeTreeTokenValue,
) {
  const url = "/?rest_route=/rekai/v1/posts";
  const [postList, setPostList] = useState([]);
  const hasFetchedRef = useRef(false);

  useEffect(() => {
    if (hasFetchedRef.current) {
      return;
    }
    hasFetchedRef.current = true;

    const subTreeList = [];
    const excludeTreeList = [];
    apiFetch({ path: url })
      .then((body) => {
        const list = [];
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
      })
      .catch((error) => {
        hasFetchedRef.current = false;
        console.error(error.message ?? error);
      });
    // Only ever needs to fetch once per mounted instance.
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  return postList;
}
