/*
    likelihood.c
    $Id: likelihood.c,v 1.1 2004/11/05 08:28:15 dmochiha Exp $

*/
#include <stdlib.h>
#include <math.h>
#include "likelihood.h"
#include "feature.h"
#include "dmatrix.h"
#include "util.h"

double
lda_lik (document *data, double **beta, double **gammas, int m, int nclass)
{
	double **egammas;
	double z, lik;
	document *dp;
	int i, j, k;
	int n;
	lik = 0;
	
	if ((egammas = dmatrix(m, nclass)) == NULL) {
		fprintf(stderr, "lda_likelihood:: cannot allocate egammas.\n");
		exit(1);
	}
	normalize_matrix_row(egammas, gammas, m, nclass);
	
	for (dp = data, i = 0; (dp->len) != -1; dp++, i++)
	{
		n = dp->len;
		for (j = 0; j < n; j++) {
			for (k = 0, z = 0; k < nclass; k++)
				z += beta[dp->id[j]][k] * egammas[i][k];
			lik += dp->cnt[j] * log(z);
		}
	}

	free_dmatrix(egammas, m);
	return lik;

}

