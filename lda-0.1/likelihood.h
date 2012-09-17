/*
    likelihood.h
    $Id: likelihood.h,v 1.1 2004/11/05 08:28:17 dmochiha Exp $

*/
#ifndef LDA_LIKELIHOOD_H
#define LDA_LIKELIHOOD_H
#include "feature.h"

extern double lda_lik (document *data, double **beta, double **gammas,
		       int m, int nclass);


#endif
