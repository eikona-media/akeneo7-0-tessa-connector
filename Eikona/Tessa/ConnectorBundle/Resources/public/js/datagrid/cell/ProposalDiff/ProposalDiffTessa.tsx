import React from 'react';
import {diffArrays} from 'diff';
import {useRouter} from '@akeneo-pim-community/shared';
import {ProposalChangeAccessor} from '@akeneopimworkflow/js/datagrid/cell/ProposalChange';
import styled from 'styled-components';

type ProposalDiffTessaProps = {
  accessor: ProposalChangeAccessor;
  change: {
    before: string[] | null;
    after: string[] | null;
  };
};

const TessaImages = styled.div`
  display: flex;
  flex-flow: wrap;
  gap: 5px;
  max-width: 330px;
`;
const TessaImageWrapper = styled.div`
  border-radius: 5px;
  border: 1px solid #c7cbd4;
`;
const TessaImage = styled.img`
  object-fit: contain;
  width: 60px;
  height: 60px;
  padding: 5px;
`
const TessaImageAssetId = styled.div`
  text-align: center;
`;

const ProposalDiffTessa: React.FC<ProposalDiffTessaProps> = ({accessor, change, ...rest}) => {
  const router = useRouter();
  const elements: JSX.Element[] = [];
  console.log(change);
  diffArrays(change.before || [], change.after || []).forEach(change => {
    if (accessor === 'before' && change.removed) {
      change.value.forEach(value => {
        const src = router.generate('eikona_tessa_media_preview', {assetId: value});
        elements.push(<del key={`ProposalDiffStringArray-${elements.length}`}>
          <TessaImageWrapper>
            <TessaImage src={src} />
            <TessaImageAssetId>{value}</TessaImageAssetId>
          </TessaImageWrapper>
        </del>);
      });
    } else if (accessor === 'after' && change.added) {
      change.value.forEach(value => {
        const src = router.generate('eikona_tessa_media_preview', {assetId: value});
        elements.push(<ins key={`ProposalDiffStringArray-${elements.length}`}>
          <TessaImageWrapper>
            <TessaImage src={src} />
            <TessaImageAssetId>{value}</TessaImageAssetId>
          </TessaImageWrapper>
        </ins>);
      });
    } else if ((accessor === 'before' && !change.added) || (accessor === 'after' && !change.removed)) {
      change.value.forEach(value => {
        const src = router.generate('eikona_tessa_media_preview', {assetId: value});
        elements.push(<span key={`ProposalDiffStringArray-${elements.length}`}>
          <TessaImageWrapper>
            <TessaImage src={src} />
            <TessaImageAssetId>{value}</TessaImageAssetId>
          </TessaImageWrapper>
        </span>);
      });
    }
  });

  if (elements.length) {
    return (
      <TessaImages {...rest}>
        {elements}
      </TessaImages>
    );
  }

  return <span {...rest} />;
};

export default ProposalDiffTessa;
