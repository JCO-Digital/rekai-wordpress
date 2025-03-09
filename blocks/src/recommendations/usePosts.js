import { useEffect, useState } from "@wordpress/element";
import { useEntityRecords } from "@wordpress/core-data";

export default function usePosts(separator) {
  const [postList, setPostList] = useState([]);

  /*
  const { isResolving, records } = useEntityRecords(
		"root",
		"postType",
		queryArgs,
	);
	*/

  const { isResolving, records } = useEntityRecords("postType", "page", {
    per_page: -1,
  });

  useEffect(() => {
    if (records) {
      let tokenList = [];
      setPostList(
        records.map((record) => {
          console.debug(record);
          const token =
            record.title.rendered + separator + record.id.toString();
          //if (subtreeIds.includes(record.id.toString())) {
          //  tokenList.push(token);
          //}
          return token;
        }),
      );
      //setTokenValue(tokenList);
    }
  }, [records]);

  return {
    postList,
    loading: isResolving,
  };
}
