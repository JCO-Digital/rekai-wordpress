import { useState } from "@wordpress/element";

let fetching = false;

export default function usePosts(subtree, setTokenValue, separator) {
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
        const tokenList = [];
        response.json().then((body) => {
          let list = [...postList];
          body.forEach((post) => {
            const index = post.id ? post.id : post.link;
            const token = post.label + separator + index;
            list.push(token);
            if (subtree.includes(index)) {
              tokenList.push(token);
            }
          });
          setTokenValue(tokenList);
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
